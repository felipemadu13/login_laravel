<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PhotoRequest extends FormRequest
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
            'image' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'dimensions: min_width=80,min_height=80,max_width=1000,max_height=1000',
                'max:2048'
            ]
        ];
    }

    public function messages(): array
    {
        return [
            'required'=> 'O campo :attribute é o obrigatório.',
            'image' => 'O arquivo deve ser uma imagem',
            'mimes' => 'A imagem devem estar em um desses formatos: jpg, jpeg ou png',
            'dimensions' => 'A imagem deve ser de no mínimo 80x80 e de no máximo 1000x1000',
            'image' => 'O arquivo deve ser inferior a 2MB'

        ];
    }
}
