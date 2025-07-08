<?php

namespace Tests\Feature\Http\Controllers\admin;

use Illuminate\Foundation\Testing\WithFaker;
use \Symfony\Component\HttpFoundation\Response;
use Tests\Feature\FeatureBaseTestCase;
use App\Models\User;
use App\Enums\Role;

class UserControllerTest extends FeatureBaseTestCase
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
        $response = $this->get('/api/admin/user');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_index_with_role(): void
    {
        $role = 'instructor';
        $response = $this->get('/api/admin/user?role=' . $role);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $response->json());
        foreach ($response->json() as $user) {
            $this->assertEquals($role, $user['role']);
        }
    }

    public function test_index_with_name(): void
    {
        $name = '二郎';
        $response = $this->get('/api/admin/user?name=' . $name);

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
        $response = $this->get('/api/admin/user?role=' . $role . '&name=' . $name);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, $response->json());
        foreach ($response->json() as $user) {
            $this->assertEquals('生徒三郎', $user['name']);
        }
    }

    public function test_create_with_valid_data(): void
    {
        $response = $this->post('/api/admin/user', [
            'name' => 'テスト一太郎',
            'email' => 'test1ta@hogehoge.com',
            'role' => 'instructor',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);
    }

    public function test_create_with_invalid_data(): void
    {
        $response = $this->post('/api/admin/user', [
            'name' => '',
            'email' => 'test1ta@hogehoge.com',
            'role' => 'instructor',
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = $response->json();
        $this->assertArrayHasKey('name', $data['errors']);

        $response = $this->post('/api/admin/user', [
            'name' => 'テスト一太郎',
            'email' => '',
            'role' => 'instructor',
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = $response->json();
        $this->assertArrayHasKey('email', $data['errors']);

        $response = $this->post('/api/admin/user', [
            'name' => 'テスト一太郎',
            'email' => 'test1ta@hogehoge.com',
            'role' => '',
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = $response->json();
        $this->assertArrayHasKey('role', $data['errors']);
    }
}
