<?php

use Valithor\Exception\InvalidSchemaException;
use Valithor\Schema\Schema;

mutates(Schema::class);

beforeEach(function () {
    Mockery::close();
});

describe('parseSafe', function () {
    it('accepts a valid value', function () {
        $schema = Mockery::mock(Schema::class)->makePartial();
        $schema->shouldAllowMockingProtectedMethods();

        $schema->shouldReceive('parseData')->andReturnUsing(fn($value) => $value)->once();

        $expected = 'John Doe';
        $result = $schema->parseSafe($expected);

        expect($result->isValid())->toBeTrue()
            ->and($result->issues)->toBeNull()
            ->and($result->value)->toBe($expected);
    });

    it('returns an issue if given null for a required schema', function () {
        $schema = Mockery::mock(Schema::class)->makePartial();

        $result = $schema->parseSafe(null);

        expect($result->isValid())->toBeFalse()
            ->and($result->issues)->toHaveCount(1)
            ->and($result->issues[0]?->message)->toBe('The value is required.');
    });

    it('returns null if schema is optional', function () {
        $schema = Mockery::mock(Schema::class)->makePartial();

        $result = $schema
            ->optional()
            ->parseSafe(null);

        expect($result->isValid())->toBeTrue()
            ->and($result->issues)->toBeNull()
            ->and($result->value)->toBeNull();
    });

    it('returns default value if schema is optional', function () {
        $schema = Mockery::mock(Schema::class)->makePartial();

        $defaultValue = 'John Doe';
        $result = $schema
            ->optional()
            ->default($defaultValue)
            ->parseSafe(null);

        expect($result->isValid())->toBeTrue()
            ->and($result->issues)->toBeNull()
            ->and($result->value)->toBe($defaultValue);
    });

    it('returns produced default value if schema is optional', function () {
        $schema = Mockery::mock(Schema::class)->makePartial();

        $defaultValue = 'John Doe';
        $result = $schema
            ->optional()
            ->default(fn() => $defaultValue)
            ->parseSafe(null);

        expect($result->isValid())->toBeTrue()
            ->and($result->issues)->toBeNull()
            ->and($result->value)->toBe($defaultValue);
    });
});

describe('parse', function () {
    it('returns a valid value on parse', function () {
        $schema = Mockery::mock(Schema::class)->makePartial();
        $schema->shouldAllowMockingProtectedMethods();

        $schema->shouldReceive('parseData')->andReturnUsing(fn($value) => $value)->once();

        $expected = 'John Doe';
        $result = $schema->parse($expected);

        expect($result)->toBe($expected);
    });

    it('throws an exception if the schema is invalid', function () {
        $schema = Mockery::mock(Schema::class)->makePartial();

        expect(fn() => $schema->parse(null))
            ->toThrow(InvalidSchemaException::class);
    });
});

describe('refinements', function () {
    it('validates through a refinement', function () {
        $schema = Mockery::mock(Schema::class)->makePartial();
        $schema->shouldAllowMockingProtectedMethods();

        $schema->shouldReceive('parseData')->andReturnUsing(fn($value) => $value)->once();

        $expected = 'John Doe';

        $result = $schema->refine(fn($value) => is_string($value))->parseSafe($expected);

        expect($result->isValid())->toBeTrue()
            ->and($result->issues)->toBeEmpty()
            ->and($result->value)->toBe($expected);
    });

    it('fails a refinement', function () {
        $schema = Mockery::mock(Schema::class)->makePartial();
        $schema->shouldAllowMockingProtectedMethods();

        $schema->shouldReceive('parseData')->andReturnUsing(fn($value) => $value)->once();

        $expected = 'John Doe';

        $result = $schema->refine(fn($value) => is_null($value), 'Value must be null')->parseSafe($expected);

        expect($result->isValid())->toBeFalse()
            ->and($result->value)->toBeNull()
            ->and($result->issues)->toHaveCount(1)
            ->and($result->issues[0]?->message)->toBe('Value must be null');
    });

    it('transforms data', function () {
        $schema = Mockery::mock(Schema::class)->makePartial();
        $schema->shouldAllowMockingProtectedMethods();

        $schema->shouldReceive('parseData')->andReturnUsing(fn($value) => $value)->once();

        $expected = 'John Doe';

        $result = $schema->transform(fn($value) => $value . ' Doe')->parseSafe('John');

        expect($result->isValid())->toBeTrue()
            ->and($result->value)->toBe($expected);
    });
});
