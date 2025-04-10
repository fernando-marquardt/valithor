<?php
declare(strict_types=1);

namespace Valithor\Exception;

use Valithor\Result\Issue;

abstract class SchemaException extends \Exception
{
    /**
     * @param Issue[] $issues
     */
    public function __construct(
        public readonly array $issues,
    )
    {
        parent::__construct('The data provided for this schema is invalid.');
    }
}
