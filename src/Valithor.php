<?php
declare(strict_types=1);

namespace Valithor;

use Valithor\Schema\ObjectSchema;
use Valithor\Schema\Schema;
use Valithor\Schema\StringSchema;

final class Valithor
{
    /**
     * @template T
     * @param array<string,Schema<T>> $schemas
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
