<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreInspectionRequest extends FormRequest
{
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:180'],
            't_brand' => ['required', 'string', 'max:60'],
            't_model' => ['required', 'string', 'max:60'],
            't_year' => ['required', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            't_comuna' => ['required', 'string', 'max:80'],
            't_plate' => ['nullable', 'string', 'max:10'],
            't_listing_url' => ['nullable', 'url', 'max:500'],
            'message' => ['nullable', 'string', 'max:2000'],
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
            't_brand' => 'marca',
            't_model' => 'modelo',
            't_year' => 'año',
            't_comuna' => 'comuna',
            't_plate' => 'patente',
            't_listing_url' => 'enlace de la publicación',
            'message' => 'mensaje',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'website.size' => 'No pudimos procesar el formulario.',
            't_comuna.required' => 'Necesitamos saber dónde está el auto para coordinar la visita.',
            't_listing_url.url' => 'Pega el enlace completo, partiendo por https://',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $digitos = preg_replace('/\D+/', '', (string) $this->input('phone'));

                if (strlen((string) $digitos) < 8 || strlen((string) $digitos) > 15) {
                    $validator->errors()->add(
                        'phone',
                        'Escribe un teléfono válido, por ejemplo +56 9 1234 5678.'
                    );
                }
            },
        ];
    }
}
