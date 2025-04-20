<?php
declare(strict_types=1);

namespace Valithor\Parse;

use Valithor\Result\Issue;

class ParseContext
{
    /**
     * @var string[]
     */
    private array $innerPath = [];

    /**
     * @var Issue[]
     */
    private(set) array $issues = [];

    public string $path {
        get => implode('.', $this->innerPath);
    }

    public function addIssue(string $message): void
    {
        $this->issues[] = Issue::make($this->path, $message);
    }

    public function hasIssues(): bool
    {
        return count($this->issues) > 0;
    }

    public function pushPath(string $path): void
    {
        $this->innerPath[] = $path;
    }

    public function popPath(): string|null
    {
        return array_pop($this->innerPath);
    }
}
