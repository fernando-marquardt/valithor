<?php

use Valithor\Exception\InvalidObjectException;
use Valithor\Valithor;

it('parses an array with two elements', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
    ];

    $result = Valithor::object([
        'name' => Valithor::string(),
        'email' => Valithor::string(),
    ])->parse($data);

    expect($result->name)->toBe($data['name'])
        ->and($result->email)->toBe($data['email']);
});

it('parses an empty array', function () {
    $data = [];

    Valithor::object([
        'name' => Valithor::string(),
        'email' => Valithor::string(),
    ])->parse($data);
})->throws(InvalidObjectException::class);

it('extends another object schema', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
    ];

    $result = Valithor::object([
        'name' => Valithor::string(),
    ])->extend(Valithor::object([
        'email' => Valithor::string(),
    ]))->parse($data);

    expect($result->name)->toBe($data['name'])
        ->and($result->email)->toBe($data['email']);
});

it('extends an array of schemas', function () {
    $data = [
        'name' => 'John Doe',
        'email' => 'john@doe.com',
    ];

    $result = Valithor::object([
        'name' => Valithor::string(),
    ])->extend([
        'email' => Valithor::string(),
    ])->parse($data);

    expect($result->name)->toBe($data['name'])
        ->and($result->email)->toBe($data['email']);
});
