<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class FilamentAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@sureshotsupply.test'],
            [
                'name' => 'SureShot Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
