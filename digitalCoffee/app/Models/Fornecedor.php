<?php

namespace App\Models;

use Database\Factories\FornecedorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nome', 'cnpj', 'email', 'telefone'])]
class Fornecedor extends Model
{
    /** @use HasFactory<FornecedorFactory> */
    use HasFactory;

    protected $table = 'fornecedores';

    public function produtos(): HasMany
    {
        return $this->hasMany(Produto::class);
    }
}
