<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreEmployeesPost extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $max="max:255";
        return [
            'name' => ['required', 'string', $max,'regex:/^[\pL\s\-]+$/u'],
            'email' => ['required', 'string', 'email'],
            'position' => ['required'],
            'birthdate'=>['required', 'date'],
            'address'=>['required'],
            'skills'=>['required'],
        ];
    }

    public function messages()
    {
        return [
            'name.required'=>'Su nombre es requerido.',
            'email.required'=>'Su correo electrónico es requerido.',
            'email.email'=>'El formato de correo electrónico  es incorrecto, intenta nuevamente.',
            'position.required' => 'El puesto es requerido.',
            'birthdate.required'=> 'La fecha de cumpleaños es requerida',
            'address.required'=> 'La dirección es requerida',
            'skills.required'=> 'Las habilidades son requeridas',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $response = response()->json([
            'statusCode' => 422,
            'message'    => 'Unprocessable Entity',
            'errors'     => $validator->errors()
        ], 422);
        throw new HttpResponseException($response);
    }
}
