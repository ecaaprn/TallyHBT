<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['nama' => 'Operator 1', 'username' => 'operator1', 'email' => 'operator1@wflo.com', 'password' => Hash::make('12345')],
            ['nama' => 'Operator 2', 'username' => 'operator2', 'email' => 'operator2@wflo.com', 'password' => Hash::make('23456')],
            ['nama' => 'Operator 3', 'username' => 'operator3', 'email' => 'operator3@wflo.com', 'password' => Hash::make('34567')],
            ['nama' => 'Operator 4', 'username' => 'operator4', 'email' => 'operator4@wflo.com', 'password' => Hash::make('45678')],
            ['nama' => 'Operator 5', 'username' => 'operator5', 'email' => 'operator5@wflo.com', 'password' => Hash::make('56789')],
            ['nama' => 'Operator 6', 'username' => 'operator6', 'email' => 'operator6@wflo.com', 'password' => Hash::make('67890')],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['username' => $userData['username']], $userData);
        }
    }
}
