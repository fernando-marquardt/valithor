<?php
declare(strict_types=1);

namespace Valithor\Result;

/**
 * @template T
 */
readonly class ValidationResult
{
    /**
     * @param bool $valid
     * @param T $value
     * @param Issue[]|null $issues
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
     * @param T $value
     * @return self
     */
    public static function valid(mixed $value): self
    {
        return new self(true, $value);
    }

    /**
     * @param Issue[] $issues
     * @return self
     */
    public static function invalid(array $issues): self
    {
        return new self(false, issues: $issues);
    }

    public static function invalidIssue(string $path, string $message): self
    {
        return self::invalid([Issue::make($path, $message)]);
    }
}
