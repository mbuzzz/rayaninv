<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        Role::create([
            'name' => 'superadmin',
            'display_name' => 'Super Administrator',
        ]);

        Role::create([
            'name' => 'staff',
            'display_name' => 'Staff',
        ]);
    }
}
