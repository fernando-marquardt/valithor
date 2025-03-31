<?php
declare(strict_types=1);

namespace Valithor\Schema;

use stdClass;
use Valithor\Exception\InvalidObjectException;
use Valithor\Exception\SchemaException;
use Valithor\Schema;

/**
 * @template T
 * @extends Schema<object>
 */
class ObjectSchema extends Schema
{
    public function __construct(
        /**
         * @var Schema[]
         */
        private array $schemas = [],
    )
    {
    }

    /**
     * @param Schema[] $schemas
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
     * @param object|array $data
     * @return object
     * @throws InvalidObjectException If the data object has any invalid element.
     */
    protected function parseData(mixed $data): object
    {
        $parsedValue = new stdClass();

        $issues = [];

        foreach ($this->schemas as $key => $schema) {
            try {
                $parsedValue->{$key} = $schema->parse($data[$key] ?? null);
            } catch (SchemaException $e) {
                $issues[$key] = $e->getMessage();
            }
        }

        if (count($issues) > 0) {
            throw new InvalidObjectException($issues);
        }

        return $parsedValue;
    }
}
