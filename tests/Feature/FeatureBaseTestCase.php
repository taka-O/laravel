<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;
use App\Models\User;
use App\Enums\Role;

class FeatureBaseTestCase extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        
        Artisan::call('migrate:fresh --seed');
    }

    public function getHeaders(string $role = 'admin')
    {
        $role_type = Role::getByName($role)->value;
        if ($role_type == null) {
            $role_type = Role::Admin->value;
        }
        $login_user = User::factory()->create(['name' => 'アドミン太郎', 'password' => bcrypt('password'), 'role_type' => $role_type]);

        return $this->getHeadersByUser($login_user);
    }

    public function getHeadersByUser(User $user)
    {
        // ログイン処理
        $res = $this->post('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
        $data = json_decode($res->content(), true);

        return ['Authorization' => 'Bearer ' . $data['token']];
    }
}
