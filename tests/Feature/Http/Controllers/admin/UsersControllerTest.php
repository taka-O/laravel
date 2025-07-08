<?php

namespace Tests\Feature\Http\Controllers\admin;

use Illuminate\Foundation\Testing\WithFaker;
use \Symfony\Component\HttpFoundation\Response;
use Tests\Feature\FeatureBaseTestCase;
use App\Models\User;
use App\Enums\Role;

class UsersControllerTest extends FeatureBaseTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        
        User::factory()->create(['name' => 'アドミン太郎']);
        User::factory()->create(['name' => '講師太郎', 'role_type' => Role::instructor->value]);
        User::factory()->create(['name' => '講師二郎', 'role_type' => Role::instructor->value]);
        User::factory()->create(['name' => '講師三郎', 'role_type' => Role::instructor->value]);
        User::factory()->create(['name' => '生徒太郎', 'role_type' => Role::student->value]);
        User::factory()->create(['name' => '生徒二郎', 'role_type' => Role::student->value]);
        User::factory()->create(['name' => '生徒三郎', 'role_type' => Role::student->value]);
        User::factory()->create(['name' => '生徒四郎', 'role_type' => Role::student->value]);
    }

    public function test_index(): void
    {
        $response = $this->get('/api/admin/users');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_index_with_role(): void
    {
        $role = 'instructor';
        $response = $this->get('/api/admin/users?role=' . $role);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $response->json());
        foreach ($response->json() as $user) {
            $this->assertEquals($role, $user['role']);
        }
    }

    public function test_index_with_name(): void
    {
        $name = '二郎';
        $response = $this->get('/api/admin/users?name=' . $name);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(2, $response->json());
        $pattern = "/$name/";
        foreach ($response->json() as $user) {
            $this->assertMatchesRegularExpression($pattern, $user['name']);
        }
    }

    public function test_index_with_name_and_name(): void
    {
        $role = 'student';
        $name = '三郎';
        $response = $this->get('/api/admin/users?role=' . $role . '&name=' . $name);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, $response->json());
        foreach ($response->json() as $user) {
            $this->assertEquals('生徒三郎', $user['name']);
        }
    }

    public function test_careate(): void
    {
        $response = $this->post('/api/admin/users', [
            'name' => 'テスト一太郎',
            'emal' => 'test1ta@hogehoge.com',
            'password' => 'hogehoge',
            'role' => 'instructor',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }
}
