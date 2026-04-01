<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserByAdminRequest extends FormRequest
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
            //
            'nom' => ['required', 'string', 'min:2'], 
            'email' => ['required', 'string','email','max:99','unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', 'string','exists:roles,name']
        ];
    }
}
