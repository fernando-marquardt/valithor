<?php
declare(strict_types=1);

namespace Valithor\Result;

/**
 * @template T
 */
readonly class ParsingResult
{
    /**
     * @param bool $valid
     * @param ($valid is true ? T|null : null) $value
     * @param ($valid is false ? Issue[] : null) $issues
     */
    private function __construct(
        private bool $valid,
        private(set) mixed $value = null,
        private(set) ?array $issues = null,
    )
    {
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @template R
     * @param R|null $value
     * @return self<R>
     */
    public static function valid(mixed $value): self
    {
        return new self(true, $value);
    }

    /**
     * @param Issue[] $issues
     * @return self<null>
     */
    public static function invalid(array $issues): self
    {
        return new self(false, issues: $issues);
    }
}
