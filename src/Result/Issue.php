<?php
declare(strict_types=1);

namespace Valithor\Result;

readonly class Issue
{
    private function __construct(
        public string $path,
        public string $message,
    )
    {
    }

    public static function make(string $path, string $message): self
    {
        return new self($path, $message);
    }
}
