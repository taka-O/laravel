<?php

namespace Tests\Feature\Http\Controllers\admin;

use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\FeatureBaseTestCase;
use App\Models\User;
use App\Enums\Role;

class UserControllerTest extends FeatureBaseTestCase
{
    public $headers;

    public function setUp(): void
    {
        parent::setUp();

        User::factory()->create(['name' => '講師太郎', 'role_type' => Role::instructor->value]);
        User::factory()->create(['name' => '講師二郎', 'role_type' => Role::instructor->value]);
        User::factory()->create(['name' => '講師三郎', 'role_type' => Role::instructor->value]);
        User::factory()->create(['name' => '生徒太郎', 'role_type' => Role::student->value]);
        User::factory()->create(['name' => '生徒二郎', 'role_type' => Role::student->value]);
        User::factory()->create(['name' => '生徒三郎', 'role_type' => Role::student->value]);
        User::factory()->create(['name' => '生徒四郎', 'role_type' => Role::student->value]);
    }

    public function getHeaders(string $role = 'admin')
    {
        $role_type = Role::getByName($role)->value;
        if ($role_type == null) {
            $role_type = Role::Admin->value;
        }
        $login_user = User::factory()->create(['name' => 'アドミン太郎', 'password' => Hash::make('password'), 'role_type' => $role_type]);

        // ログイン処理
        $res = $this->post('/api/auth/login', [
            'email' => $login_user->email,
            'password' => 'password',
        ]);
        $data = json_decode($res->content(), true);

        return ['Authorization' => 'Bearer ' . $data['token']];
    }

    public function test_index(): void
    {
        $response = $this->withHeaders($this->getHeaders())->get('/api/admin/users');

        $response->assertStatus(Response::HTTP_OK);
    }

    public function test_index_with_role(): void
    {
        $role = 'instructor';
        $response = $this->withHeaders($this->getHeaders())->get('/api/admin/users?role=' . $role);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(3, $response->json());
        foreach ($response->json() as $user) {
            $this->assertEquals($role, $user['role']);
        }
    }

    public function test_index_with_name(): void
    {
        $name = '二郎';
        $response = $this->withHeaders($this->getHeaders())->get('/api/admin/users?name=' . $name);

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
        $response = $this->withHeaders($this->getHeaders())->get('/api/admin/users?role=' . $role . '&name=' . $name);

        $response->assertStatus(Response::HTTP_OK);
        $this->assertCount(1, $response->json());
        foreach ($response->json() as $user) {
            $this->assertEquals('生徒三郎', $user['name']);
        }
    }

    public function test_create_with_valid_data(): void
    {
        $response = $this->withHeaders($this->getHeaders())->post('/api/admin/users', [
            'name' => 'テスト一太郎',
            'email' => 'test1ta@hogehoge.com',
            'role' => 'instructor',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $data = $response->json();
        $this->assertEquals('テスト一太郎', $data['name']);
        $this->assertEquals('test1ta@hogehoge.com', $data['email']);
        $this->assertEquals('instructor', $data['role']);
    }

    public function test_create_with_no_authority_user(): void
    {
        $headers = $this->getHeaders('instructor');
        $response = $this->withHeaders($headers)->post('/api/admin/users', [
            'name' => 'テスト一太郎',
            'email' => 'test1ta@hogehoge.com',
            'role' => 'instructor',
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_create_with_invalid_data(): void
    {
        $response = $this->withHeaders($this->getHeaders())->post('/api/admin/users', [
            'name' => '',
            'email' => 'test1ta@hogehoge.com',
            'role' => 'instructor',
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = $response->json();
        $this->assertArrayHasKey('name', $data['errors']);

        $response = $this->withHeaders($this->getHeaders())->post('/api/admin/users', [
            'name' => 'テスト一太郎',
            'email' => '',
            'role' => 'instructor',
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = $response->json();
        $this->assertArrayHasKey('email', $data['errors']);

        $response = $this->withHeaders($this->getHeaders())->post('/api/admin/users', [
            'name' => 'テスト一太郎',
            'email' => 'test1ta@hogehoge.com',
            'role' => '',
        ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $data = $response->json();
        $this->assertArrayHasKey('role', $data['errors']);
    }

    public function test_update_with_valid_data(): void
    {
        $user = User::factory()->create(['name' => 'テスト一太郎', 'password' => Hash::make('password'), 'email' => 'test1ta@hogehoge.com', 'role_type' => Role::admin->value]);
        $response = $this->withHeaders($this->getHeaders())->put('/api/admin/users/' . $user->id, [
            'name' => 'アップデート一太郎',
            'email' => 'update1ta@hogehoge.com',
            'role' => 'instructor',
        ]);

        $response->assertStatus(Response::HTTP_CREATED);

        $data = $response->json();
        $this->assertEquals('アップデート一太郎', $data['name']);
        $this->assertEquals('update1ta@hogehoge.com', $data['email']);
        $this->assertEquals('instructor', $data['role']);
    }

    public function test_update_with_not_exist_user(): void
    {
        $user = User::factory()->create(['name' => 'テスト一太郎', 'password' => Hash::make('password'), 'email' => 'test1ta@hogehoge.com', 'role_type' => Role::admin->value]);
        $response = $this->withHeaders($this->getHeaders())->put('/api/admin/users/' . $user->id + 100, [
            'name' => 'アップデート一太郎',
            'email' => 'update1ta@hogehoge.com',
            'role' => 'instructor',
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
    }

    public function test_update_with_no_authority_user(): void
    {
        $user = User::factory()->create(['name' => 'テスト一太郎', 'password' => Hash::make('password'), 'email' => 'test1ta@hogehoge.com', 'role_type' => Role::admin->value]);
        $headers = $this->getHeaders('student');
        $response = $this->withHeaders($headers)->put('/api/admin/users/' . $user->id, [
            'name' => 'アップデート一太郎',
            'email' => 'update1ta@hogehoge.com',
            'role' => 'instructor',
        ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }
}
