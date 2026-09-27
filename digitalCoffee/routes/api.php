<?php

use App\Http\Controllers\Api\CategoriaController;
use App\Http\Controllers\Api\FornecedorController;
use App\Http\Controllers\Api\ProdutoController;
use Illuminate\Support\Facades\Route;

Route::get('produtos/paginados', [ProdutoController::class, 'listagemPaginada'])->name('produtos.paginados');

Route::get('categorias', [CategoriaController::class, 'listagem'])->name('categorias.listagem');
Route::post('categorias', [CategoriaController::class, 'cadastrar'])->name('categorias.cadastrar');
Route::get('categorias/{categoria}', [CategoriaController::class, 'obterPeloId'])->whereNumber('categoria')->name('categorias.obter');
Route::put('categorias/{categoria}', [CategoriaController::class, 'atualizar'])->whereNumber('categoria')->name('categorias.atualizar');
Route::delete('categorias/{categoria}', [CategoriaController::class, 'remover'])->whereNumber('categoria')->name('categorias.remover');

Route::get('fornecedores', [FornecedorController::class, 'listagem'])->name('fornecedores.listagem');
Route::post('fornecedores', [FornecedorController::class, 'cadastrar'])->name('fornecedores.cadastrar');
Route::get('fornecedores/{fornecedor}', [FornecedorController::class, 'obterPeloId'])->whereNumber('fornecedor')->name('fornecedores.obter');
Route::put('fornecedores/{fornecedor}', [FornecedorController::class, 'atualizar'])->whereNumber('fornecedor')->name('fornecedores.atualizar');
Route::delete('fornecedores/{fornecedor}', [FornecedorController::class, 'remover'])->whereNumber('fornecedor')->name('fornecedores.remover');

Route::get('produtos', [ProdutoController::class, 'listagem'])->name('produtos.listagem');
Route::post('produtos', [ProdutoController::class, 'cadastrar'])->name('produtos.cadastrar');
Route::get('produtos/{produto}', [ProdutoController::class, 'obterPeloId'])->whereNumber('produto')->name('produtos.obter');
Route::put('produtos/{produto}', [ProdutoController::class, 'atualizar'])->whereNumber('produto')->name('produtos.atualizar');
Route::delete('produtos/{produto}', [ProdutoController::class, 'remover'])->whereNumber('produto')->name('produtos.remover');
