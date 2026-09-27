<?php

namespace App\Models;

use Database\Factories\ProdutoFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nome', 'descricao', 'preco', 'estoque', 'categoria_id', 'fornecedor_id'])]
class Produto extends Model
{
    /** @use HasFactory<ProdutoFactory> */
    use HasFactory;

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function fornecedor(): BelongsTo
    {
        return $this->belongsTo(Fornecedor::class);
    }
}
