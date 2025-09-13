<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        DB::table('categories')->insert([
            [
                'nombre' => 'Dragon Ball',
                'slug' => Str::slug('Dragon Ball'),
                'descripcion' => 'Categoría de Dragon Ball',
                'activo' => true,
                'orden' => 1,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Naruto',
                'slug' => Str::slug('Naruto'),
                'descripcion' => 'Categoría de Naruto',
                'activo' => true,
                'orden' => 2,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'One Piece',
                'slug' => Str::slug('One Piece'),
                'descripcion' => 'Categoría de One Piece',
                'activo' => true,
                'orden' => 3,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Marvel & DC',
                'slug' => Str::slug('Marvel & DC'),
                'descripcion' => 'Categoría de Marvel y DC',
                'activo' => true,
                'orden' => 4,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Pokémon',
                'slug' => Str::slug('Pokémon'),
                'descripcion' => 'Categoría de Pokémon',
                'activo' => true,
                'orden' => 5,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Harry Potter',
                'slug' => Str::slug('Harry Potter'),
                'descripcion' => 'Categoría de Harry Potter',
                'activo' => true,
                'orden' => 6,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Demon Slayer',
                'slug' => Str::slug('Demon Slayer'),
                'descripcion' => 'Categoría de Kimetsu no Yaiba',
                'activo' => true,
                'orden' => 7,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Series y caricaturas',
                'slug' => Str::slug('Series y caricaturas'),
                'descripcion' => 'Categoría de series y caricaturas',
                'activo' => true,
                'orden' => 8,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Otros animes/series',
                'slug' => Str::slug('Otros animes series'),
                'descripcion' => 'Categoría de otros animes y series',
                'activo' => true,
                'orden' => 9,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'nombre' => 'Star Wars',
                'slug' => Str::slug('Star Wars'),
                'descripcion' => 'Categoría de Star Wars',
                'activo' => true,
                'orden' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        ]);
    }
}