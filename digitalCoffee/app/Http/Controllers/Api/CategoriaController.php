<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArmazenarCategoriaRequest;
use App\Http\Requests\AtualizarCategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\JsonResponse;

class CategoriaController extends Controller
{
    public function listagem(): JsonResponse
    {
        return response()->json(Categoria::query()->orderBy('nome')->get());
    }

    public function cadastrar(ArmazenarCategoriaRequest $requisicao): JsonResponse
    {
        $categoria = Categoria::query()->create($requisicao->validated());

        return response()->json($categoria, 201);
    }

    public function obterPeloId(Categoria $categoria): JsonResponse
    {
        return response()->json($categoria);
    }

    public function atualizar(AtualizarCategoriaRequest $requisicao, Categoria $categoria): JsonResponse
    {
        $categoria->update($requisicao->validated());

        return response()->json($categoria->refresh());
    }

    public function remover(Categoria $categoria): JsonResponse
    {
        $categoria->delete();

        return response()->json(status: 204);
    }
}
