<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Act as the given user with Sanctum authentication guard.
     *
     * This helper method wraps Laravel's actingAs() with the 'sanctum' guard
     * for stateless token authentication. Returns $this for fluent chaining.
     *
     * @param  User  $user  The user to authenticate as
     */
    protected function actingAsUser(User $user): static
    {
        return $this->actingAs($user, 'sanctum');
    }
}
