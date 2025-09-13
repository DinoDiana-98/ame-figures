<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $users = [
            [
                'name' => 'Administrador',
                'email' => 'admin@catalogo.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Usuario Normal',
                'email' => 'user@catalogo.com',
                'password' => Hash::make('password'), 
                'is_admin' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ];

        DB::table('users')->insert($users);

        $this->command->info('✅ Usuarios creados:');
        $this->command->info('👑 Admin: admin@catalogo.com / password');
        $this->command->info('👤 User: user@catalogo.com / password');
    }
}