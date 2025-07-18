<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\FeatureBaseTestCase;
use App\Models\User;
use App\Enums\Role;

class AuthControllerTest extends FeatureBaseTestCase
{    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_login_with_valid_params_and_test_me(): void
    {
        $password = 'hogehoge';
        $user = User::factory()->create(['password' => bcrypt($password), 'role_type' => Role::instructor->value]);
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(Response::HTTP_OK);

        $data = $response->json();
        $this->assertNotEmpty($data['token']);
        $this->assertEquals($user->name, $data['user']['name']);
        $this->assertEquals($user->email, $data['user']['email']);
        $this->assertEquals($user->role, $data['user']['role']);
    }

    public function test_login_with_invalid_params(): void
    {
        $password = 'hogehoge';
        $user = User::factory()->create(['password' => bcrypt($password), 'role_type' => Role::instructor->value]);
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'hogehogehoge',
        ]);

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
      }

    public function test_logout(): void
    {
        $password = 'hogehoge';
        $user = User::factory()->create(['password' => bcrypt($password), 'role_type' => Role::instructor->value]);
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $data = $response->json();
        $headers = ['Authorization' => 'Bearer ' . $data['token']];

        $response = $this->withHeaders($headers)->post('/api/auth/logout');
        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }

    public function test_current(): void
    {
        $password = 'hogehoge';
        $user = User::factory()->create(['password' => bcrypt($password), 'role_type' => Role::instructor->value]);
        $response = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $data = $response->json();
        $headers = ['Authorization' => 'Bearer ' . $data['token']];

        $response = $this->withHeaders($headers)->post('/api/auth/current');
        $response->assertStatus(Response::HTTP_OK);
        $data = $response->json();
        $this->assertEquals($user->name, $data['name']);
        $this->assertEquals($user->email, $data['email']);
        $this->assertEquals($user->role, $data['role']);
    }
}