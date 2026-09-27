<?php

namespace Database\Factories;

use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Produto>
 */
class ProdutoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $falso = fake('pt_BR');

        return [
            'nome' => $falso->words(3, true),
            'descricao' => $falso->sentence(),
            'preco' => $falso->randomFloat(2, 1, 1000),
            'estoque' => $falso->numberBetween(0, 500),
            'categoria_id' => Categoria::factory(),
            'fornecedor_id' => Fornecedor::factory(),
        ];
    }
}
