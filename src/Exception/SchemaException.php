<?php
declare(strict_types=1);

namespace Valithor\Exception;

abstract class SchemaException extends \Exception
{
    public function __construct(
        /**
         * @var string[]
         */
        private readonly array $issues,
    )
    {
        parent::__construct();
    }

    /**
     * @return string[]
     */
    public function getIssues(): array
    {
        return $this->issues;
    }
}
