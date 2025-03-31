<?php
declare(strict_types=1);

namespace Valithor;

use Valithor\Exception\SchemaException;

/**
 * Base class for all schema types.
 *
 * @template T
 */
abstract class Schema
{
    protected bool $required = true;

    /**
     * @var mixed|callable():T
     */
    protected mixed $defaultValue = null;

    /**
     * @var (callable(T):(bool|string))[]
     */
    protected array $checks = [];

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
        if (!$this->required && $data === null) {
            return $this->getDefaultValue();
        }

        return $this->parseData($data);
    }

    /**
     * @param T $data
     * @return T
     * @throws SchemaException If the data is not valid.
     */
    abstract protected function parseData(mixed $data): mixed;

    /**
     * Add a check to be called at the check phase.
     *
     * @param string $name A unique name to identify the check.
     * @param callable(T):(bool|string) $callback The check callback.
     * @return void
     */
    protected function check(string $name, callable $callback): void
    {
        $this->checks[$name] = $callback;
    }

    /**
     * @param mixed $data
     * @return array{bool,string[]} Returns an array with the first element being true if the validation passed and the second with the issues otherwise.
     */
    protected function runChecks(mixed $data): array
    {
        $valid = true;
        $issues = [];

        foreach ($this->checks as $callback) {
            if (($issue = call_user_func($callback, $data)) !== true) {
                $valid = false;
                $issues[] = $issue;
            }
        }

        return [$valid, $issues];
    }

    /**
     * @return T
     */
    private function getDefaultValue(): mixed
    {
        if (is_callable($this->defaultValue)) {
            return call_user_func($this->defaultValue);
        }

        return $this->defaultValue;
    }
}
