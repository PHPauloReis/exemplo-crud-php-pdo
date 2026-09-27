<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Seeder;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
            'Cama, mesa e banho',
            'Higiêne',
            'Higiêne pessoal',
            'Medicamentos',
            'Cereais',
            'Biscoitos',
            'Carnes',
            'Laticíneos',
            'Hortifruti',
            'Bebidas',
            'Brinquedos',
            'Instrumentos Musicais',
            'Eletrônicos',
            'Jogos',
            'Portáteis',
            'Vestuário',
            'Livros',
            'Informática',
            'Música',
            'Eletrodomésticos',
        ];

        foreach ($categorias as $nome) {
            Categoria::query()->firstOrCreate(['nome' => $nome]);
        }
    }
}
