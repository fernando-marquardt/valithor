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
     * @param Schema<mixed>[]|ObjectSchema $schemas
     * @return $this
     */
    public function extend(array|ObjectSchema $schemas): self
    {
        if ($schemas instanceof ObjectSchema) {
            $this->schemas = array_merge($this->schemas, $schemas->schemas);
        } else {
            $this->schemas = array_merge($this->schemas, $schemas);
        }

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
                foreach ($result->issues as $issue) {
                    $path = implode('.', array_filter([$key, $issue->path]));
                    $issues[] = Issue::make($path, $issue->message);
                }
            }
        }

        if (count($issues) > 0) {
            throw new InvalidObjectException($issues);
        }

        return $parsedValue;
    }
}
