<?php
declare(strict_types=1);

namespace Valithor;

use Valithor\Schema\ObjectSchema;
use Valithor\Schema\StringSchema;

final class Valithor
{
    /**
     * @param array<string,Schema<mixed>> $schemas
     * @return ObjectSchema
     */
    public static function object(array $schemas): ObjectSchema
    {
        return new ObjectSchema($schemas);
    }

    public static function string(): StringSchema
    {
        return new StringSchema();
    }
}
