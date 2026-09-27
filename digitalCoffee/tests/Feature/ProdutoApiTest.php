<?php

use App\Models\Categoria;
use App\Models\Fornecedor;
use App\Models\Produto;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

it('cria um produto e associa categoria e fornecedor', function () {
    $categoria = Categoria::factory()->create();
    $fornecedor = Fornecedor::factory()->create();

    $resposta = $this->postJson('/api/produtos', [
        'nome' => 'Cafeteira brasileira',
        'descricao' => 'Cafeteira para uso doméstico.',
        'preco' => 199.90,
        'estoque' => 12,
        'categoria_id' => $categoria->id,
        'fornecedor_id' => $fornecedor->id,
    ]);

    $resposta->assertCreated()
        ->assertJsonPath('nome', 'Cafeteira brasileira')
        ->assertJsonPath('categoria.id', $categoria->id)
        ->assertJsonPath('fornecedor.id', $fornecedor->id);
    $this->assertDatabaseHas('produtos', [
        'nome' => 'Cafeteira brasileira',
        'categoria_id' => $categoria->id,
        'fornecedor_id' => $fornecedor->id,
    ]);
});

it('retorna 422 quando os campos obrigatórios do produto não são enviados', function () {
    $resposta = $this->postJson('/api/produtos', []);

    $resposta->assertUnprocessable()
        ->assertJsonValidationErrors([
            'nome',
            'preco',
            'estoque',
            'categoria_id',
            'fornecedor_id',
        ])
        ->assertJsonPath('errors.nome.0', 'O nome do produto é obrigatório.');
});

it('atualiza e remove um produto', function () {
    $produto = Produto::factory()->create();

    $resposta = $this->putJson("/api/produtos/{$produto->id}", [
        'nome' => 'Produto atualizado',
        'descricao' => 'Descrição atualizada.',
        'preco' => 45.50,
        'estoque' => 8,
        'categoria_id' => $produto->categoria_id,
        'fornecedor_id' => $produto->fornecedor_id,
    ]);

    $resposta->assertOk()->assertJsonPath('nome', 'Produto atualizado');
    $this->assertDatabaseHas('produtos', ['id' => $produto->id, 'estoque' => 8]);

    $this->deleteJson("/api/produtos/{$produto->id}")->assertNoContent();
    $this->assertDatabaseMissing('produtos', ['id' => $produto->id]);
});

it('retorna uma listagem paginada de produtos', function () {
    Produto::factory(16)->create();

    $this->getJson('/api/produtos/paginados')
        ->assertOk()
        ->assertJsonCount(15, 'data')
        ->assertJsonPath('total', 16);
});
