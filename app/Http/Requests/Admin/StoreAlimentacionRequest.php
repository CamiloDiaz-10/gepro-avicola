<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAlimentacionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'IDLote' => ['required','integer','exists:lotes,IDLote'],
            'IDTipoAlimento' => ['nullable','integer','exists:tipo_alimentos,IDTipoAlimento'],
            'Fecha' => ['nullable','date','before_or_equal:today'],
            'CantidadKg' => ['required','numeric','min:0'],
            'Observaciones' => ['nullable','string','max:500']
        ];
    }

    public function messages(): array
    {
        return [
            'IDLote.required' => 'El lote es requerido.',
            'CantidadKg.required' => 'La cantidad (kg) es requerida.',
        ];
    }
}
