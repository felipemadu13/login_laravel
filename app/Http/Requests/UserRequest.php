<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'firstName'=> 'required',
            'lastName'=> 'required',
            'email'=> ['required', 'email', Rule::unique('users')->ignore($this->id)],
            'cpf'=> 'required',
            'phone'=> 'required',
            'password'=> 'required'
        ];
    }

    public function messages(): array
    {
        return [
            'required'=> 'O campo : attribute é o obrigatório',
            'email.unique'=> 'Este email já consta no sistema'
        ];
    }
}
