<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBirdRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'IDLote' => ['required','integer','exists:lotes,IDLote'],
            'IDTipoGallina' => ['required','integer','exists:tipo_gallinas,IDTipoGallina'],
            'FechaNacimiento' => ['required','date','before_or_equal:today'],
            'Peso' => ['nullable','numeric','min:0','max:10000'],
            'Estado' => ['required','in:Activa,Muerta,Vendida'],
            'Foto' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:4096']
        ];
    }

    public function messages(): array
    {
        return [
            'IDLote.required' => 'El lote es obligatorio.',
            'IDTipoGallina.required' => 'El tipo de gallina es obligatorio.',
            'FechaNacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'Estado.in' => 'El estado debe ser Activa, Muerta o Vendida.'
        ];
    }
}
