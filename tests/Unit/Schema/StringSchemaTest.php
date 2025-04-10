<?php

use Valithor\Exception\InvalidStringException;
use Valithor\Valithor;

describe('min', function () {
    it('checks a string with expected minimum length', function () {
        $data = 'John Doe';
        $result = Valithor::string()->min(4)->parseSafe($data);

        expect($result->isValid())->toBeTrue()
            ->and($result->value)->toBe($data);
    });

    it('fails parsing a string with expected minimum length', function () {
        $result = Valithor::string()->min(10)->parseSafe('John Doe');

        expect($result->isValid())->toBeFalse()
            ->and($result->issues)->toHaveCount(1)
            ->and($result->issues[0]?->message)->toBe('Value must contain at least {10} character(s).');
    });
});

describe('max', function () {
    it('checks for a max length', function () {
        $expected = 'John';
        $result = Valithor::string()->max(10)->parseSafe($expected);

        expect($result->isValid())->toBeTrue()
            ->and($result->value)->toBe($expected);
    });

    it('checks for string with length higher than maximum', function () {
        $result = Valithor::string()->max(3)->parseSafe('test');

        expect($result->isValid())->toBeFalse()
            ->and($result->issues)->toHaveCount(1)
            ->and($result->issues[0]?->message)->toBe('Value must contain at most {3} character(s).');
    });
});

describe('email', function () {
    it('checks for valid email address', function ($email) {
        $result = Valithor::string()->email()->parseSafe($email);

        expect($result->isValid())->toBeTrue()
            ->and($result->value)->toBe($email);
    })->with([
        'test@io.com',
        'test.io@epam.com',
        'test.io.example+today@epam.com',
        'test-io@epam.com',
        'test@io-epam.com',
        'test-io@epam-usa.com',
        '123456789testio@epam2.com',
        'user@example.com',
        'user123@email.co.uk',
        'john.doe@company.org',
        'user_name1234@email-provider.net',
        'info@sub.domain.com',
        'name@my-email-provider.xyz',
        'user123@[192.168.1.1]',
        'john.doe@email.travel',
        '_______@domain.com',
    ]);

    it('checks for invalid email address', function ($email) {
        $result = Valithor::string()->email()->parseSafe($email);

        expect($result->isValid())->toBeFalse()
            ->and($result->issues)->toHaveCount(1)
            ->and($result->issues[0]?->message)->toBe('Value must contain a valid email address.');
    })->with([
        'test.io.com',
        'test@io@epam.com',
        'test(io"epam)example]com',
        'test"io"epam.com',
        '.test... io\today@epam.com',
        'user@invalid-tld.123',
        'user#domain.com',
        'user#domain.con',
        'spaced user@domain.info',
        'double..dots@email.org',
        '@.com',
        'user@domain with space.com',
        'user@domain..com',
    ]);
});

it('tries to parse non string data', function () {
    $result = Valithor::string()->parseSafe(4);

    expect($result->isValid())->toBeFalse()
        ->and($result->issues)->toHaveCount(1)
        ->and($result->issues[0]?->message)->toBe('Value must be a string, received {integer}.');
});
