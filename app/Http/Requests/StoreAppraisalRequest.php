<?php

namespace App\Http\Requests;

use App\Enums\VehicleCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreAppraisalRequest extends FormRequest
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
            't_version' => ['nullable', 'string', 'max:60'],
            't_year' => ['required', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            't_mileage_km' => ['required', 'integer', 'min:0', 'max:2000000'],
            't_condition' => ['required', Rule::enum(VehicleCondition::class)],
            't_comuna' => ['nullable', 'string', 'max:80'],
            't_plate' => ['nullable', 'string', 'max:10'],
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
            't_version' => 'versión',
            't_year' => 'año',
            't_mileage_km' => 'kilometraje',
            't_condition' => 'estado del auto',
            't_comuna' => 'comuna',
            't_plate' => 'patente',
            'message' => 'mensaje',
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'website.size' => 'No pudimos procesar el formulario.',
            't_condition.required' => 'Elige en qué estado está el auto.',
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if (! $this->hasValidPhone()) {
                    $validator->errors()->add(
                        'phone',
                        'Escribe un teléfono válido, por ejemplo +56 9 1234 5678.'
                    );
                }
            },
        ];
    }

    private function hasValidPhone(): bool
    {
        $digits = preg_replace('/\D+/', '', (string) $this->input('phone'));

        return strlen((string) $digits) >= 8 && strlen((string) $digits) <= 15;
    }
}
