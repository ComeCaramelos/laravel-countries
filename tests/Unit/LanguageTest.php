<?php

use Webpatser\Countries\Countries;
use Webpatser\Countries\Helpers\CountryHelper;
use Webpatser\Countries\Rules\ValidLanguageCode;
use Illuminate\Support\Collection;

beforeEach(function () {
    $this->countries = new Countries();
});

describe('Countries::getByLanguage()', function () {
    it('returns countries by language code', function () {
        $countries = $this->countries->getByLanguage('es');

        expect(array_keys($countries))->toContain('ES');
        expect($countries)->toBeArray()->not->toBeEmpty();
    });

    it('returns countries with multiple languages including the requested one', function () {
        $multilingualCountries = $this->countries->getByLanguage('en');
        
        expect($multilingualCountries)->toBeArray()->not->toBeEmpty();
        
        foreach ($multilingualCountries as $country) {
            expect($country['languages'])->toBeArray()->not->toBeEmpty();
            expect($country['languages'])->toContain('en');
        }
    });

    it('returns empty array for invalid language code', function () {
        $invalidCountries = $this->countries->getByLanguage('zz');
        
        expect($invalidCountries)->toBeArray()->toBeEmpty();
    });
});

describe('Helpers\CountryHelper::getByLanguage()', function () {
    it('returns countries by language code via helper', function () {
        $spanishCountries = CountryHelper::getByLanguage('es');
        
        expect($spanishCountries)->toBeArray()->not->toBeEmpty();
    });

    it('returns empty array for invalid language code via helper', function () {
        $invalidCountries = CountryHelper::getByLanguage('xx');
        
        expect($invalidCountries)->toBeArray()->toBeEmpty();
    });
});

describe('Rules\ValidLanguageCode', function () {
    it('validates a correct ISO 639-1 language code', function () {
        $rule = new ValidLanguageCode();
        $failed = false;

        $rule->validate('language', 'es', function () use (&$failed) {
            $failed = true;
        });

        expect($failed)->toBeFalse();
    });

    it('rejects an invalid ISO 639-1 language code', function () {
        $rule = new ValidLanguageCode();
        $failed = false;

        $rule->validate('language', 'zz', function () use (&$failed) {
            $failed = true;
        });

        expect($failed)->toBeTrue();
    });

    it('allows empty values to pass validation', function () {
        $rule = new ValidLanguageCode();
        $failed = false;

        $rule->validate('language', '', function () use (&$failed) {
            $failed = true;
        });

        expect($failed)->toBeFalse();
    });

    it('rejects language codes with wrong length', function () {
        $rule = new ValidLanguageCode();
        $failed = false;

        $rule->validate('language', 'spa', function () use (&$failed) {
            $failed = true;
        });

        expect($failed)->toBeTrue();
    });
});

describe('Macros\CountryMacros', function () {
    it('filters collection by language code', function () {
        $collection = collect([
            ['name' => 'Spain', 'languages' => ['es', 'ca']],
            ['name' => 'Mexico', 'languages' => ['es']],
            ['name' => 'France', 'languages' => ['fr']],
        ]);
        
        $spanish = $collection->byLanguage('es');
        
        expect($spanish)->toHaveCount(2);
        expect($spanish->pluck('name'))->toContain('Spain');
        expect($spanish->pluck('name'))->toContain('Mexico');
    });

    it('returns empty collection for invalid language code', function () {
        $collection = collect([
            ['name' => 'Spain', 'languages' => ['es']],
            ['name' => 'France', 'languages' => ['fr']],
        ]);
        
        $invalid = $collection->byLanguage('zz');
        
        expect($invalid)->toHaveCount(0);
    });
});
