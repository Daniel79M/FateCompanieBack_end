<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator as ValidationValidator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterRequest extends FormRequest
{
    /**
     * Create a new class instance.
     */


    public function authorize(): bool
    {
        return true;
    }
    

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:64|min:5',
            'firstname' => 'required|string|max:64|min:5',
            'phone_number' => 'required|string|max:64|min:5|unique:users',
            'email' =>'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'password_confirm' => 'same:password',
            'image' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Le nom est requis',
            'name.min' => 'Le nom doit contenire au moin 3 caracters',
            'name.max' => 'Le nom doit ne doit pas dépassé 255 characters',
            'email.required' => 'L\'Email est requis',
            'email.unique' => 'L\'Email existe déjà',
            'password.required' => 'le mot de passe est requis',
            'password.same' => 'le mot de passe n\'est pas confirmé',
        ];
    }

    public function failedValidation(ValidationValidator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success'   => false,
            'message'   => 'Echec de validation.',
            'data'      => $validator->errors()
        ]));
    }
}
