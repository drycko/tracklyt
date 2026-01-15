<?php

// get currency name by code
if (!function_exists('get_currency_name')) {
    function get_currency_name($currencyCode): string
    {
        $countries = get_countries();
        foreach ($countries as $country) {
            if (isset($country['currency']['code']) && $country['currency']['code'] === $currencyCode) {
                return $country['currency']['name'];
            }
        }
        return $currencyCode; // Fallback to code if name not found
    }
}

// get currency symbol by code
if (!function_exists('get_currency_symbol')) {
    function get_currency_symbol($currencyCode = null): string
    {
        
    if ($currencyCode === null) {
        $currencyCode = config('app.currency');
    }
        $countries = get_countries();
        foreach ($countries as $country) {
            if (isset($country['currency']['code']) && $country['currency']['code'] === $currencyCode) {
                return $country['currency']['symbol'];
            }
        }
        return '$'; // Fallback to dollar sign
    }
}

/*
I want to first read the countries from my json file and
return them as an array
*/
if (!function_exists('get_countries')) {
    /**
     * Get the list of countries from the JSON file.
     *
     * @return array
     */
    function get_countries(): array
    {   
        $filePath = public_path('vendor/countries.json');
        
        if (!file_exists($filePath)) {
            // Return a basic fallback with common countries/currencies
            return [
                [
                    'name' => 'South Africa',
                    'code' => 'ZA',
                    'currency' => ['code' => 'ZAR', 'name' => 'South African Rand', 'symbol' => 'R']
                ],
                [
                    'name' => 'United States',
                    'code' => 'US',
                    'currency' => ['code' => 'USD', 'name' => 'US Dollar', 'symbol' => '$']
                ],
                [
                    'name' => 'United Kingdom',
                    'code' => 'GB',
                    'currency' => ['code' => 'GBP', 'name' => 'British Pound', 'symbol' => '£']
                ],
                [
                    'name' => 'European Union',
                    'code' => 'EU',
                    'currency' => ['code' => 'EUR', 'name' => 'Euro', 'symbol' => '€']
                ]
            ];
        }
        
        $json = file_get_contents($filePath);
        $countries = json_decode($json, true);
        // order by name
        usort($countries, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });
        return $countries;
    }
}

if (!function_exists('get_countries_list')) {
    /**
     * Get the list of countries as code => name pairs.
     *
     * @return array
     */
    function get_countries_list(): array
    {
        $countries = get_countries();
        $countryList = [];
        foreach ($countries as $country) {
            $countryList[$country['code']] = $country['name'];
        }
        return $countryList;
    }
}

// get currencies from countries.json
if (!function_exists('get_currencies')) {
    /**
     * Get the list of unique currencies from the countries JSON file.
     *
     * @return array
     */
    function get_currencies(): array
    {
        $countries = get_countries();
        $currencies = [];
        foreach ($countries as $country) {
            if (isset($country['currency']['code']) && !in_array($country['currency']['code'], $currencies)) {
                $currencies[] = $country['currency']['code'];
            }
        }
        sort($currencies);
        return $currencies;
    }
}

// format money with currency 
if (!function_exists('format_money')) {
    /**
     * Format a money with the given currency.
     *
     * @param float|int $amount The amount to format
     * @param string|null $currency The currency code (e.g., USD, ZAR). If null, app default currency
     * @param bool $showCurrency Whether to show the currency code
     * @return string
     */
    function format_money($amount, $currency = null, $showCurrency = true): string
    {
        if ($currency === null) {
            $currency = config('app.currency');
        }
        
        // Get currency symbol
        $symbol = get_currency_symbol($currency);
        
        $formattedAmount = number_format((float) $amount, 2, '.', ',');
        
        return $showCurrency ? "{$symbol}{$formattedAmount}" : $formattedAmount;
    }
}
/**
 * Get all banks
 */
if (!function_exists('get_banks')) {
    function get_banks($activeOnly = true, $country = null): \Illuminate\Support\Collection
    {
        $query = \App\Models\Bank::query();
        
        if ($activeOnly) {
            $query->active();
        }
        
        if ($country) {
            $query->byCountry($country);
        }
        
        return $query->orderBy('name')->get();
    }
}

/**
 * Get banks by country
 */
if (!function_exists('get_banks_by_country')) {
    function get_banks_by_country($country, $activeOnly = true): \Illuminate\Support\Collection
    {
        return get_banks($activeOnly, $country);
    }
}

/**
 * Get bank countries (unique list of countries that have banks)
 */
if (!function_exists('get_bank_countries')) {
    function get_bank_countries(): array
    {
        return \App\Models\Bank::active()
            ->distinct()
            ->orderBy('country')
            ->pluck('country')
            ->toArray();
    }
}

/**
 * Get supported countries for the platform
 */
if (!function_exists('get_supported_countries')) {
    function get_supported_countries(): array
    {
        return array_values(config('app.supported_countries', [
            'South Africa',
            'Lesotho',
            'Eswatini',
        ]));
    }
}

/**
 * Get country code from country name
 */
if (!function_exists('get_country_code')) {
    function get_country_code($countryName): ?string
    {
        $countries = get_countries();
        foreach ($countries as $country) {
            if ($country['name'] === $countryName) {
                return $country['code'];
            }
        }
        return null;
    }
}

/**
 * Get country name from country name
 */
if (!function_exists('get_country_name')) {
    function get_country_name($countryCode): ?string
    {
        $countries = get_countries();
        foreach ($countries as $country) {
            if ($country['code'] === $countryCode) {
                return $country['name'];
            }
        }
        return null;
    }
}

/**
 * Convert markdown to HTML
 */
if (!function_exists('markdown')) {
    function markdown(?string $text): string
    {
        if (empty($text)) {
            return '';
        }

        $converter = new \League\CommonMark\CommonMarkConverter([
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);

        return $converter->convert($text)->getContent();
    }
}

/**
 * truncate
 */
if (!function_exists('truncate')) {
    /**
     * Truncate a string to a specified length.
     *
     * @param string $text The text to truncate
     * @param int $length The maximum length
     * @param string $suffix The suffix to append if truncated
     * @return string
     */
    function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (strlen($text) <= $length) {
            return $text;
        }
        return substr($text, 0, $length) . $suffix;
    }
}


/**
 * tenant
 */
if (!function_exists('user_tenant')) {
    /**
     * Get the current authenticated user's tenant.
     *
     * @return \App\Models\Tenant|null
     */
    function user_tenant(): ?\App\Models\Tenant
    {
        $user = auth()->user();
        return $user ? $user->tenant : null;
    }
}
