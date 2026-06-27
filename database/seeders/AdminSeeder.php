<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Pastikan role superadmin ada
        $superadmin = Role::firstOrCreate(
            ['name' => 'superadmin'],
            ['display_name' => 'Super Administrator']
        );

        // Buat/update user admin rayan
        User::updateOrCreate(
            ['username' => 'rayan'],
            [
                'name'     => 'Admin Rayan',
                'email'    => 'rayan@rayan.web.id',
                'username' => 'rayan',
                'password' => Hash::make('@r4y4n#c0M'),
                'role_id'  => $superadmin->id,
            ]
        );
    }
}
