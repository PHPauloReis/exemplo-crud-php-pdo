<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AtualizarProdutoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'preco' => ['required', 'numeric', 'min:0'],
            'estoque' => ['required', 'integer', 'min:0'],
            'categoria_id' => ['required', 'integer', 'exists:categorias,id'],
            'fornecedor_id' => ['required', 'integer', 'exists:fornecedores,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do produto é obrigatório.',
            'preco.required' => 'O preço é obrigatório.',
            'preco.numeric' => 'O preço deve ser numérico.',
            'estoque.required' => 'O estoque é obrigatório.',
            'estoque.integer' => 'O estoque deve ser um número inteiro.',
            'categoria_id.required' => 'A categoria é obrigatória.',
            'categoria_id.exists' => 'A categoria informada não existe.',
            'fornecedor_id.required' => 'O fornecedor é obrigatório.',
            'fornecedor_id.exists' => 'O fornecedor informado não existe.',
        ];
    }
}
