<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GuardarFirmaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ruta pública de firma
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'nombres_niños.*'           => 'sometimes|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'apellidos_niños.*'         => 'sometimes|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'fechas_nacimiento_niños.*' => 'sometimes|date|before:today',
            'participantes_existentes'  => 'array',
            'aceptar_terminos'          => 'required|accepted',
            'firma_base64'              => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'nombres_niños.*.regex'              => 'El nombre solo debe contener letras.',
            'apellidos_niños.*.regex'            => 'El apellido solo debe contener letras.',
            'fechas_nacimiento_niños.*.before'   => 'La fecha de nacimiento debe ser anterior a hoy.',
            'aceptar_terminos.accepted'          => 'Debe aceptar los términos de responsabilidad para continuar.',
            'firma_base64.required'              => 'La firma digital es obligatoria. Por favor, firme el acuerdo.',
            'firma_base64.string'                => 'La firma digital enviada no tiene un formato válido.',
        ];
    }

    /**
     * Validación adicional de lógica de negocio (mínimo un niño, máximo 15).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $totalExistentes = count($this->input('participantes_existentes', []));
            $totalNuevos     = count($this->input('nombres_niños', []));
            $total           = $totalExistentes + $totalNuevos;

            if ($total === 0) {
                $validator->errors()->add(
                    'error_niños',
                    'Debe seleccionar al menos un niño registrado o añadir uno nuevo para continuar.'
                );
            }

            if ($total > 15) {
                $validator->errors()->add(
                    'error_niños',
                    'Un representante solo puede registrar un máximo de 15 niños.'
                );
            }
        });
    }
}
