<?php
declare(strict_types=1);

use Valithor\Schema;

it('checks for an optional schema', function () {
    $schema = Mockery::mock(Schema::class)->makePartial();

    $result = $schema
        ->optional()
        ->parse(null);

    expect($result)->toBeNull();
});

it('checks for an optional schema with a default value', function () {
    $schema = Mockery::mock(Schema::class)->makePartial();

    $defaultValue = 'John Doe';
    $result = $schema
        ->optional()
        ->default($defaultValue)
        ->parse(null);

    expect($result)->toBe($defaultValue);
});

it('checks for an optional schema with a producer for the default value', function () {
    $schema = Mockery::mock(Schema::class)->makePartial();

    $defaultValue = 'John Doe';
    $result = $schema
        ->optional()
        ->default(fn() => $defaultValue)
        ->parse(null);

    expect($result)->toBe($defaultValue);
});
