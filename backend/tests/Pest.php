<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Pest PHP Configuration
|--------------------------------------------------------------------------
|
| This file configures global test bindings for Pest PHP.
| - All Feature and Unit tests extend the base TestCase
| - All Feature tests use LazilyRefreshDatabase for isolated DB state
|
*/

uses(TestCase::class)->in('Feature', 'Unit');
uses(LazilyRefreshDatabase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| Global custom expectations can be defined here.
| Example: expect()->extend('toBeOne', fn () => $this->toBe(1));
|
*/

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| Global helper functions can be defined here.
| Keep this minimal; prefer TestCase methods for shared logic.
|
*/
