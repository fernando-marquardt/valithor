<?php

use Valithor\Schema\ObjectSchema;
use Valithor\Valithor;

mutates(ObjectSchema::class);

describe('parse', function () {
    it('parses an array with two elements', function () {
        $expected = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
        ];

        $result = Valithor::object([
            'name' => Valithor::string(),
            'email' => Valithor::string(),
        ])->parseSafe($expected);

        expect($result->isValid())->toBeTrue()
            ->and($result->value->name)->toBe($expected['name'])
            ->and($result->value->email)->toBe($expected['email']);
    });

    it('parses an empty array', function () {
        $data = [];

        $result = Valithor::object([
            'name' => Valithor::string(),
            'email' => Valithor::string(),
        ])->parseSafe($data);

        expect($result->isValid())->toBeFalse()
            ->and($result->issues)->toHaveCount(2)
            ->and($result->issues[0]->path)->toBe('name')
            ->and($result->issues[0]->message)->toBe('The value is required.')
            ->and($result->issues[1]->path)->toBe('email')
            ->and($result->issues[1]->message)->toBe('The value is required.');
    });

    it('parses an object with two properties', function () {
        $expected = new stdClass();
        $expected->name = 'John Doe';
        $expected->email = 'john@doe.com';

        $result = Valithor::object([
            'name' => Valithor::string(),
            'email' => Valithor::string(),
        ])->parseSafe($expected);

        expect($result->isValid())->toBeTrue()
            ->and($result->value->name)->toBe($expected->name)
            ->and($result->value->email)->toBe($expected->email);
    });

    it('fails with a value of invalid type', function () {
        $schema = Valithor::object([
            'name' => Valithor::string(),
        ]);

        $result = $schema->parseSafe('John Doe');

        expect($result->isValid())->toBeFalse()
            ->and($result->issues)->toHaveCount(1)
            ->and($result->issues[0]->message)->toBe('Value must be an object or an array, received {string}.');
    });
});

describe('extend', function () {
    it('extends another object schema', function () {
        $expected = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
        ];

        $result = Valithor::object([
            'name' => Valithor::string(),
        ])->extend(Valithor::object([
            'email' => Valithor::string(),
        ]))->parseSafe($expected);

        expect($result->isValid())->toBeTrue()
            ->and($result->value->name)->toBe($expected['name'])
            ->and($result->value->email)->toBe($expected['email']);
    });

    it('extends an array of schemas', function () {
        $expected = [
            'name' => 'John Doe',
            'email' => 'john@doe.com',
        ];

        $result = Valithor::object([
            'name' => Valithor::string(),
        ])->extend([
            'email' => Valithor::string(),
        ])->parseSafe($expected);

        expect($result->isValid())->toBeTrue()
            ->and($result->value->name)->toBe($expected['name'])
            ->and($result->value->email)->toBe($expected['email']);
    });
});
