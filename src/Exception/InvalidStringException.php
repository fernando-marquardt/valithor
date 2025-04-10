<?php
declare(strict_types=1);

namespace Valithor\Exception;

use Valithor\Result\Issue;

class InvalidStringException extends SchemaException
{
    public static function invalidType(string $type): self
    {
        return new self([Issue::make('', 'Value must be a string, received {' . $type . '}.')]);
    }
}
