<?php
declare(strict_types=1);

namespace Valithor\Schema;

use Valithor\Exception\InvalidStringException;
use Valithor\Schema;

/**
 * @template T
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
        $this->check('min', function (string $value) use ($min, $message) {
            if (strlen($value) < $min) {
                return $message ?? 'String value must contain at least {' . $min . '} character(s).';
            }

            return true;
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
        $this->check('max', function (string $value) use ($max, $message) {
            if (strlen($value) > $max) {
                return $message ?? 'String value must contain at most {' . $max . '} character(s).';
            }

            return true;
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
        $this->check('email', function (string $value) use ($message) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
                return $message ?? 'String value must contain a valid email address.';
            }

            return true;
        });

        return $this;
    }

    /**
     * @param string $data
     * @return string
     * @throws InvalidStringException If the data value is not valid.
     */
    protected function parseData(mixed $data): string
    {
        if ($data === null && $this->required) {
            throw new InvalidStringException(['String value is required.']);
        }

        if (!is_string($data)) {
            throw new InvalidStringException(['String value must be a string.']);
        }

        $data = (string)$data;

        list($valid, $issues) = $this->runChecks($data);

        if (!$valid) {
            throw new InvalidStringException($issues);
        }

        return $data;
    }
}
