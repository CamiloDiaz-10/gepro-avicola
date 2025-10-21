<?php

namespace App\Http\Requests\Admin;

use App\Models\Lote;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreProduccionHuevosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'IDLote' => ['required','integer','exists:lotes,IDLote'],
            'Fecha' => ['nullable','date','before_or_equal:today'],
            'CantidadHuevos' => ['required','integer','min:0','max:100000'],
            'HuevosRotos' => ['nullable','integer','min:0','lte:CantidadHuevos'],
            'Turno' => ['nullable','in:Mañana,Tarde,Noche'],
            'PesoPromedio' => ['nullable','numeric','min:0','max:200'],
            'Observaciones' => ['nullable','string','max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'IDLote.required' => 'El lote es requerido.',
            'IDLote.exists' => 'El lote seleccionado no existe.',
            'CantidadHuevos.required' => 'La cantidad de huevos es requerida.',
            'CantidadHuevos.integer' => 'La cantidad de huevos debe ser un número entero.',
            'HuevosRotos.lte' => 'Los huevos rotos no pueden superar la cantidad total.',
            'Fecha.before_or_equal' => 'La fecha no puede ser futura.',
            'Turno.in' => 'El turno debe ser Mañana, Tarde o Noche.',
        ];
    }

    /**
     * Configure el validador para agregar validación personalizada
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if (!$validator->errors()->has('IDLote') && !$validator->errors()->has('CantidadHuevos')) {
                $loteId = $this->input('IDLote');
                $cantidadHuevos = $this->input('CantidadHuevos');

                try {
                    $lote = Lote::find($loteId);
                    
                    if ($lote) {
                        $validacion = $lote->validarCantidadHuevos($cantidadHuevos);
                        
                        if (!$validacion['valido']) {
                            $validator->errors()->add('CantidadHuevos', $validacion['mensaje']);
                        }
                    }
                } catch (\Exception $e) {
                    // Si hay error, no agregamos validación adicional
                }
            }
        });
    }
}
