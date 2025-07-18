<?php

namespace Database\Seeders;

use App\Models\User;
use App\Enums\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::where("role_type", Role::admin->value)->first();

        if ($user === null) {
            User::factory()->create([
                'pid' => (string) Str::uuid(),
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('Passw0rd'),
                'role' => Role::admin,
            ]);
        }
    }
}
