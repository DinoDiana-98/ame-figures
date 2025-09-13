<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Primero usuarios (para evitar dependencias)
        $this->call(AdminUserSeeder::class);
        
        // 2. Luego categorías (los productos dependen de categorías)
        $this->call(CategorySeeder::class);
        
        // 3. Finalmente productos (necesitan categorías existentes)
        $this->call(ProductSeeder::class);

        $this->command->info('✅ Todos los seeders ejecutados en el orden correcto');
    }
}
