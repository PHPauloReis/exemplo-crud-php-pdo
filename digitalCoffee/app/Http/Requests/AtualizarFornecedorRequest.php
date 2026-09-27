<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class AtualizarFornecedorRequest extends FormRequest
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
            'cnpj' => ['required', 'string', 'max:18', 'unique:fornecedores,cnpj,'.$this->route('fornecedor')->id],
            'email' => ['required', 'email', 'max:255', 'unique:fornecedores,email,'.$this->route('fornecedor')->id],
            'telefone' => ['required', 'string', 'max:20'],
        ];
    }

    public function messages(): array
    {
        return [
            'nome.required' => 'O nome do fornecedor é obrigatório.',
            'cnpj.required' => 'O CNPJ é obrigatório.',
            'cnpj.unique' => 'Já existe um fornecedor com este CNPJ.',
            'email.required' => 'O e-mail é obrigatório.',
            'email.email' => 'O e-mail informado é inválido.',
            'email.unique' => 'Já existe um fornecedor com este e-mail.',
            'telefone.required' => 'O telefone é obrigatório.',
        ];
    }
}
