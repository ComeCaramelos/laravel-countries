<?php

namespace Webpatser\Countries\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Webpatser\Countries\Countries;

class ValidLanguageCode implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $countries = new Countries();
        $allCountries = $countries->getList();

        $isValid = false;

        // Validar código ISO 639-1 (2 letras, ej: 'es', 'en', 'fr')
        foreach ($allCountries as $country) {
            $languages = $country['languages'] ?? null;
            if (! $languages || ! in_array($value, $languages, true)) continue;

            $isValid = true;
            break;
        }

        if (! $isValid) {
            $fail("The {$attribute} must be a valid language code (ISO 639-1).");
        }
    }
}
