<?php

namespace Database\Factories;

use App\Models\Fornecedor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Fornecedor>
 */
class FornecedorFactory extends Factory
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
            'nome' => $falso->company(),
            'cnpj' => $falso->unique()->cnpj(false),
            'email' => $falso->unique()->companyEmail(),
            'telefone' => $falso->cellphoneNumber(),
        ];
    }
}
