<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreInquiryRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:180'],
            'message' => ['nullable', 'string', 'max:2000'],
            'vehicle_id' => ['nullable', 'integer', 'exists:vehicles,id'],
            'website' => ['nullable', 'size:0'],
        ];
    }

    /** @return array<string, string> */
    public function attributes(): array
    {
        return [
            'name' => 'nombre',
            'phone' => 'teléfono',
            'email' => 'correo',
            'message' => 'mensaje',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'website.size' => 'No pudimos procesar el formulario.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $digits = preg_replace('/\D+/', '', (string) $this->input('phone'));

                if (strlen((string) $digits) < 8 || strlen((string) $digits) > 15) {
                    $validator->errors()->add(
                        'phone',
                        'Escribe un teléfono válido, por ejemplo +56 9 1234 5678.'
                    );
                }
            },
        ];
    }
}
