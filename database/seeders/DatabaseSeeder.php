<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(RoleSeeder::class);

        $superadmin = Role::where('name', 'superadmin')->first();

        User::factory()->create([
            'name' => 'Admin Rayan',
            'username' => 'admin',
            'email' => 'admin@rayan.web.id',
            'role_id' => $superadmin?->id,
        ]);
    }
}
