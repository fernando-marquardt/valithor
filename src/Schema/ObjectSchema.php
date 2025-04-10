<?php
declare(strict_types=1);

namespace Valithor\Schema;

use ArrayObject;
use Override;
use stdClass;
use Valithor\Exception\InvalidObjectException;
use Valithor\Result\Issue;
use Valithor\Schema;

/**
 * @extends Schema<object>
 */
class ObjectSchema extends Schema
{
    public function __construct(
        /**
         * @var array<string,Schema<mixed>>
         */
        private array $schemas,
    )
    {
    }

    /**
     * @param Array<string, Schema<mixed>>|ObjectSchema $schemas
     * @return $this
     */
    public function extend(array|ObjectSchema $schemas): self
    {
        $this->schemas = array_merge($this->schemas, (is_array($schemas)) ? $schemas : $schemas->schemas);

        return $this;
    }

    /**
     * @param object|array<string, mixed> $data
     * @return object
     * @throws InvalidObjectException If the data object has any invalid element.
     */
    #[Override]
    protected function parseData(mixed $data): object
    {
        if (!is_object($data) && !is_array($data)) {
            throw InvalidObjectException::invalidType(gettype($data));
        }

        $data = new ArrayObject($data);
        $parsedValue = new stdClass();

        $issues = [];

        foreach ($this->schemas as $key => $schema) {
            $result = $schema->parseSafe($data[$key] ?? null);

            if ($result->isValid()) {
                $parsedValue->{$key} = $result->value;
            } else {
                array_push($issues, ...$this->parseIssues($key, $result->issues ?? []));
            }
        }

        if (count($issues) > 0) {
            throw new InvalidObjectException($issues);
        }

        return $parsedValue;
    }

    /**
     * @param string $path
     * @param Issue[] $issues
     * @return Issue[]
     */
    private function parseIssues(string $path, array $issues): array
    {
        return array_map(function ($issue) use ($path) {
            $path = implode('.', array_filter([$path, $issue->path]));

            return Issue::make($path, $issue->message);
        }, $issues);
    }
}
