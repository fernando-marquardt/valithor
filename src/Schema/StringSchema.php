<?php
declare(strict_types=1);

namespace Valithor\Schema;

use Override;
use Valithor\Parse\ParseContext;

/**
 * @extends Schema<string>
 */
class StringSchema extends Schema
{
    /**
     * Check if the string has at least $min amount of characters.
     *
     * @param int $min The minimum amount of characters expected. It is inclusive.
     * @param string|null $message
     * @return $this
     */
    public function min(int $min, ?string $message = null): self
    {
        $this->check(function (string $value, ParseContext $context) use ($min, $message) {
            if (strlen($value) < $min) {
                $context->addIssue($message ?? 'Value must contain at least {' . $min . '} character(s).');
            }
        });

        return $this;
    }

    /**
     * Check if the string has at most $max amount of characters.
     *
     * @param int $max The maximum amount of characters expected. It is inclusive.
     * @param string|null $message
     * @return $this
     */
    public function max(int $max, ?string $message = null): self
    {
        $this->check(function (string $value, ParseContext $context) use ($max, $message) {
            if (strlen($value) > $max) {
                $context->addIssue($message ?? 'Value must contain at most {' . $max . '} character(s).');
            }
        });

        return $this;
    }

    /**
     * Check if the string is a valid email address.
     *
     * @param string|null $message
     * @return $this
     */
    public function email(?string $message = null): self
    {
        $this->check(function (string $value, ParseContext $context) use ($message) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                $context->addIssue($message ?? 'Value must contain a valid email address.');
            }
        });

        return $this;
    }

    /**
     * @param mixed $data
     * @param ParseContext $context
     * @return string|null
     */
    #[Override]
    protected function parseData(mixed $data, ParseContext $context): string|null
    {
        if (!is_string($data)) {
            $context->addIssue('Value must be a string, received {' . gettype($data) . '}.');
            return null;
        }

        return (string)$data;
    }
}
