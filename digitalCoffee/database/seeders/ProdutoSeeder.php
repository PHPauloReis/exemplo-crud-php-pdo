<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Produto;
use Illuminate\Database\Seeder;

class ProdutoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Produto::factory(100)
            ->recycle(Categoria::query()->get())
            ->recycle(Fornecedor::query()->get())
            ->create();
    }
}
