<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        // Create admin user if it doesn't exist
        $admin = User::firstOrCreate(
            ['email' => 'admin@telehealthmart.com'],
            [
                'name' => 'Admin',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        if (!$admin->hasRole('Super Admin')) {
            $admin->assignRole('Super Admin');
        }
    }
}
