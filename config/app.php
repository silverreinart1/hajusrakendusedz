<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'name'            => env('APP_NAME', 'Hajusrakendused'),
    'env'             => env('APP_ENV', 'production'),
    'debug'           => (bool) env('APP_DEBUG', false),
    'url'             => env('APP_URL', 'http://localhost'),
    'timezone'        => 'Europe/Tallinn',
    'locale'          => 'et',
    'fallback_locale' => 'en',
    'faker_locale'    => 'et_EE',
    'cipher'          => 'AES-256-CBC',
    'key'             => env('APP_KEY'),
    'previous_keys'   => [],
    'maintenance'     => ['driver' => 'file'],
    'providers'       => ServiceProvider::defaultProviders()->merge([
        //
    ])->toArray(),
    'aliases'         => Facade::defaultAliases()->merge([
        //
    ])->toArray(),
];
