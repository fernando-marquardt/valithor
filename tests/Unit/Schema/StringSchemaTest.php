<?php

use Valithor\Exception\InvalidStringException;
use Valithor\Valithor;

describe('min', function () {
    it('checks for a minimum length', function () {
        $data = 'test';
        $result = Valithor::string()->min(4)->parse($data);

        expect($result)->toBe($data);
    });

    it('checks for string with length lesser than minimum', function () {
        Valithor::string()->min(10)->parse('test');
    })->throws(InvalidStringException::class);
});

describe('max', function () {
    it('checks for a max length', function () {
        $data = 'test';
        $result = Valithor::string()->max(10)->parse($data);

        expect($result)->toBe($data);
    });

    it('checks for string with length higher than maximum', function () {
        Valithor::string()->max(3)->parse('test');
    })->throws(InvalidStringException::class);
});

describe('email', function () {
    it('checks for valid email address', function ($email) {
        $result = Valithor::string()->email()->parse($email);

        expect($result)->toBe($email);
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
        Valithor::string()->email()->parse($email);
    })->throws(InvalidStringException::class)->with([
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
    Valithor::string()->parse(4);
})->throws(InvalidStringException::class);
