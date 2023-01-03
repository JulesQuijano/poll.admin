<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use App\Models\User;

class UserSeeder extends Seeder
{
    const SUPER_ADMIN = 1;
    const ADMIN = 2;
    const EMAIL_ADMIN = 3;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            'name' => 'Super Admin',
            'email' => 'super_admin@pupvoting.org',
            'email_verified_at' => now(),
            'password' => Hash::make('1234'),
            'role' => self::SUPER_ADMIN,
            'status' => '1',
            'slug' => 'super_admin',
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Email Admin',
            'email' => 'email_admin@pupvoting.org',
            'email_verified_at' => now(),
            'password' => Hash::make('1234'),
            'role' => self::EMAIL_ADMIN,
            'status' => '1',
            'slug' => 'email_admin',
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Admin',
            'email' => 'admin@pupvoting.org',
            'email_verified_at' => now(),
            'password' => Hash::make('1234'),
            'role' => self::ADMIN,
            'status' => '1',
            'slug' => 'admin',
            'remember_token' => Str::random(10),
        ]);
    }
}
