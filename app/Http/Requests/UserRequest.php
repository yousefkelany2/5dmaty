<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'name'      => 'required|string|max:255',
            'phone'     => 'required|string|max:15|unique:users,phone',
            'email'     => 'required|email|unique:users,email',
            'user_type' => 'in:admin,user', 
            'password'  => 'required|string|min:6|confirmed',
        ];
    }
}
