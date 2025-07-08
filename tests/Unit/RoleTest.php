<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Enums\Role;

class RoleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_getByName(): void
    {
        $role = Role::getByName('admin');
        $this->assertEquals($role, Role::admin);
    }
}
