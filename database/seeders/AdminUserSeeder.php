<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default admin account for Posko Desa Kalikurmo.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator Posko',
                'email' => 'admin@kalikurmo.desa.id',
                'password' => Hash::make('admin'),
            ]
        );
    }
}

