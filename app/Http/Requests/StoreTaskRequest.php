<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; //car pour le moment on n'a pas de mode d'authentification
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            //
            'nom' => ['required', 'string', 'min:2'], //pareil qu'on aurait mis dans un fillable de quasar2
            'task' => ['required', 'string'], 
            'date' => ['required', 'date'], 
            'done' => ['sometimes', 'boolean'],
        ];
    }
}
