<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArmazenarFornecedorRequest;
use App\Http\Requests\AtualizarFornecedorRequest;
use App\Models\Fornecedor;
use Illuminate\Http\JsonResponse;

class FornecedorController extends Controller
{
    public function listagem(): JsonResponse
    {
        return response()->json(Fornecedor::query()->orderBy('nome')->get());
    }

    public function cadastrar(ArmazenarFornecedorRequest $requisicao): JsonResponse
    {
        $fornecedor = Fornecedor::query()->create($requisicao->validated());

        return response()->json($fornecedor, 201);
    }

    public function obterPeloId(Fornecedor $fornecedor): JsonResponse
    {
        return response()->json($fornecedor);
    }

    public function atualizar(AtualizarFornecedorRequest $requisicao, Fornecedor $fornecedor): JsonResponse
    {
        $fornecedor->update($requisicao->validated());

        return response()->json($fornecedor->refresh());
    }

    public function remover(Fornecedor $fornecedor): JsonResponse
    {
        $fornecedor->delete();

        return response()->json(status: 204);
    }
}
