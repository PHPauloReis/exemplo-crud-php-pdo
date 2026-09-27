<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArmazenarProdutoRequest;
use App\Http\Requests\AtualizarProdutoRequest;
use App\Models\Produto;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;

class ProdutoController extends Controller
{
    public function listagem(): JsonResponse
    {
        $produtos = Produto::query()->orderBy('nome')->get();

        return response()->json($produtos->map($this->formatarProduto(...)));
    }

    public function cadastrar(ArmazenarProdutoRequest $requisicao): JsonResponse
    {
        $produto = Produto::query()->create($requisicao->validated());

        return response()->json($this->formatarProduto($produto), 201);
    }

    public function obterPeloId(Produto $produto): JsonResponse
    {
        return response()->json($this->formatarProduto($produto));
    }

    public function atualizar(AtualizarProdutoRequest $requisicao, Produto $produto): JsonResponse
    {
        $produto->update($requisicao->validated());

        return response()->json($this->formatarProduto($produto->refresh()));
    }

    public function remover(Produto $produto): JsonResponse
    {
        $produto->delete();

        return response()->json(status: 204);
    }

    public function listagemPaginada(): JsonResponse
    {
        /** @var LengthAwarePaginator<int, Produto> $produtos */
        $produtos = Produto::query()->orderBy('nome')->paginate(15);
        $produtos->through($this->formatarProduto(...));

        return response()->json($produtos);
    }

    /**
     * @return array<string, mixed>
     */
    private function formatarProduto(Produto $produto): array
    {
        return [
            'id' => $produto->id,
            'nome' => $produto->nome,
            'descricao' => $produto->descricao,
            'preco' => $produto->preco,
            'estoque' => $produto->estoque,
            'categoria' => $produto->categoria,
            'fornecedor' => $produto->fornecedor,
            'criado_em' => $produto->created_at,
            'atualizado_em' => $produto->updated_at,
        ];
    }
}
