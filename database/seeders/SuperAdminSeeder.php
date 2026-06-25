<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPER_ADMIN_EMAIL', 'admin@forms.uz');
        $password = env('SUPER_ADMIN_PASSWORD', 'password');
        $name = env('SUPER_ADMIN_NAME', 'Super Admin');

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => User::ROLE_SUPER_ADMIN,
            ]
        );

        $this->command?->info('Super admin yaratildi / yangilandi.');
        $this->command?->line("Email: {$user->email}");
        $this->command?->line("Parol: {$password}");
        $this->command?->line('Login: '.url('/login'));
    }
}
