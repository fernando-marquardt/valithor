<?php
declare(strict_types=1);

namespace Valithor\Schema;

use ArrayObject;
use Override;
use stdClass;
use Valithor\Parse\ParseContext;

/**
 * @extends Schema<object|array>
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
        $this->schemas = [...$this->schemas, ...((is_array($schemas)) ? $schemas : $schemas->schemas)];

        return $this;
    }

    /**
     * @param mixed $data
     * @param ParseContext $context
     * @return object|null
     */
    #[Override]
    protected function parseData(mixed $data, ParseContext $context): object|null
    {
        if (!is_object($data) && !is_array($data)) {
            $context->addIssue('Value must be an object or an array, received {' . gettype($data) . '}.');

            return null;
        }

        $data = new ArrayObject($data);
        $parsedValue = new stdClass();

        foreach ($this->schemas as $key => $schema) {
            $context->pushPath($key);
            $parsedValue->{$key} = $schema->parseWithContext($data[$key] ?? null, $context);
            $context->popPath();
        }

        return $parsedValue;
    }
}
