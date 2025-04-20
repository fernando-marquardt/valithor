<?php
declare(strict_types=1);

namespace Valithor\Schema;

use Valithor\Exception\InvalidSchemaException;
use Valithor\Parse\ParseContext;
use Valithor\Result\ParsingResult;

/**
 * Base class for all schema types.
 *
 * @template T
 * @phpstan-type Check callable(T, ParseContext):void
 * @phpstan-type Refinement callable(T, ParseContext):T
 */
abstract class Schema
{
    protected bool $required = true;

    /**
     * @var (callable():T)|T|null
     */
    protected mixed $defaultValue = null;

    /**
     * @var list<Check>
     */
    private array $checks = [];

    /**
     * @var list<Refinement>
     */
    private array $refinements = [];

    /**
     * The current schema becomes optional and won't throw validation messages if no value is loaded.
     *
     * @return $this
     */
    public function optional(): self
    {
        $this->required = false;

        return $this;
    }

    /**
     * Set a default value to be produced if none is loaded.
     *
     * @param T|callable():T $defaultValue The default value to be produced or a callable to produce the value.
     * @return $this
     */
    public function default(mixed $defaultValue): self
    {
        $this->defaultValue = $defaultValue;

        return $this;
    }

    /**
     * @param callable(T):bool $refinement
     * @param string $message
     * @return $this
     */
    public function refine(callable $refinement, string $message = 'Invalid value'): self
    {
        $this->refinements[] = function (mixed $value, ParseContext $context) use ($refinement, $message) {
            if (!$refinement($value)) {
                $context->addIssue($message);
            }

            return $value;
        };

        return $this;
    }

    /**
     * @param callable(T):T $transform
     * @return $this
     */
    public function transform(callable $transform): self
    {
        $this->refinements[] = $transform(...);

        return $this;
    }

    /**
     * Check if the data is valid. If it is, the value is returned, otherwise an error is thrown.
     *
     * @param T|null $data
     * @return T|null
     * @throws InvalidSchemaException If the data is not valid.
     */
    public function parse(mixed $data): mixed
    {
        $context = new ParseContext();
        $parsedData = $this->parseWithContext($data, $context);

        if ($context->hasIssues()) {
            throw new InvalidSchemaException($context->issues ?? []);
        }

        return $parsedData;
    }

    /**
     * @param T|null $data
     * @return ParsingResult<T>|ParsingResult<null>
     */
    public function parseSafe(mixed $data): ParsingResult
    {
        $context = new ParseContext();
        $parsedData = $this->parseWithContext($data, $context);

        if ($context->hasIssues()) {
            return ParsingResult::invalid($context->issues);
        }

        return ParsingResult::valid($parsedData);
    }

    /**
     * @param T|null $data
     * @param ParseContext $context
     * @return T|null
     */
    protected function parseWithContext(mixed $data, ParseContext $context): mixed
    {
        if ($data === null) {
            if ($this->required) {
                $context->addIssue('The value is required.');

                return null;
            }

            return $this->loadDefaultValue();
        }

        $parsedData = $this->parseData($data, $context);

        if (!$context->hasIssues()) {
            $this->applyChecks($parsedData, $context);
        }

        if (!$context->hasIssues()) {
            $parsedData = $this->applyRefinements($parsedData, $context);
        }

        return $parsedData;
    }

    /**
     * @param T $data
     * @return T
     */
    protected abstract function parseData(mixed $data, ParseContext $context): mixed;

    /**
     * Add a check to be called at the check phase.
     *
     * @param Check $callback The check callback.
     * @return void
     */
    protected function check(callable $callback): void
    {
        $this->checks[] = $callback;
    }

    /**
     * @param T $value
     * @param ParseContext $context
     * @return void
     */
    protected function applyChecks(mixed $value, ParseContext $context): void
    {
        foreach ($this->checks as $check) {
            $check($value, $context);
        }
    }

    /**
     * @param T $value
     * @param ParseContext $context
     * @return T
     */
    protected function applyRefinements(mixed $value, ParseContext $context): mixed
    {
        foreach ($this->refinements as $refinement) {
            $value = $refinement($value, $context);
        }

        return $value;
    }

    /**
     * @return T|null
     */
    protected function loadDefaultValue(): mixed
    {
        if (is_callable($this->defaultValue)) {
            return call_user_func($this->defaultValue);
        }

        return $this->defaultValue;
    }
}
