<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function createUser(): User
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        $this->assertAuthenticated();

        return $user;
    }

}
