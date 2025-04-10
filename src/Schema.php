<?php
declare(strict_types=1);

namespace Valithor;

use Valithor\Exception\InvalidSchemaException;
use Valithor\Exception\SchemaException;
use Valithor\Result\Issue;
use Valithor\Result\ValidationResult;

/**
 * Base class for all schema types.
 *
 * @template T
 */
abstract class Schema
{
    protected bool $required = true;

    /**
     * @var T|callable():T
     */
    protected mixed $defaultValue = null {
        set => $this->defaultValue = $value;

        get {
            if (is_callable($this->defaultValue)) {
                return call_user_func($this->defaultValue);
            }

            return $this->defaultValue;
        }
    }

    /**
     * @var callable(T):string[]
     */
    private array $checks = [];

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
     * Check if the data is valid. If it is, the value is returned, otherwise an error is thrown.
     *
     * @param T $data
     * @return T
     * @throws SchemaException If the data is not valid.
     */
    public function parse(mixed $data): mixed
    {
        $result = $this->parseSafe($data);

        if (!$result->isValid()) {
            throw new InvalidSchemaException($result->issues);
        }

        return $result->value;
    }

    /**
     * @param T $data
     * @return ValidationResult<T>
     */
    public function parseSafe(mixed $data): ValidationResult
    {
        if ($data === null) {
            if ($this->required) {
                return ValidationResult::invalidIssue('', 'The value is required.');
            }

            return ValidationResult::valid($this->defaultValue);
        }

        try {
            $parsedData = $this->parseData($data);
        } catch (SchemaException $e) {
            return ValidationResult::invalid($e->issues);
        }

        $issues = $this->runChecks($parsedData);

        if (count($issues) > 0) {
            return ValidationResult::invalid($issues);
        }

        return ValidationResult::valid($parsedData);
    }

    /**
     * @param T $data
     * @return T
     * @throws SchemaException
     */
    protected abstract function parseData(mixed $data): mixed;

    /**
     * Add a check to be called at the check phase.
     *
     * @param string $name A unique name to identify the check.
     * @param callable(T):string $callback The check callback.
     * @return void
     */
    protected function check(string $name, callable $callback): void
    {
        $this->checks[$name] = $callback;
    }

    /**
     * @param T $data
     * @return Issue[] Returns an array with the first element being true if the validation passed and the second with the issues otherwise.
     */
    protected function runChecks(mixed $data): array
    {
        $issues = [];

        foreach ($this->checks as $callback) {
            $message = call_user_func($callback, $data);

            if ($message) {
                $issues[] = Issue::make('', $message);
            }
        }

        return $issues;
    }
}
