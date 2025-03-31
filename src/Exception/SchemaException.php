<?php
declare(strict_types=1);

namespace Valithor\Exception;

abstract class SchemaException extends \Exception
{
    public function __construct(
        private readonly array $issues,
    )
    {
        parent::__construct();
    }

    public function getIssues(): array
    {
        return $this->issues;
    }
}
