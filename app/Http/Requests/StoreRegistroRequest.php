<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegistroRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ruta pública de registro
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'cedula'                     => 'required|numeric|digits_between:7,8|unique:representantes,cedula',
            'nombre'                     => 'required|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'apellido'                   => 'required|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'parentesco'                 => 'required|in:Padre,Madre,Representante Legal',
            'correo'                     => 'required|email|max:100',
            'telefono'                   => 'required|numeric|digits:11',
            'fecha_nacimiento'           => 'required|date|before_or_equal:' . date('Y-m-d', strtotime('-18 years')),
            'nombres_niños'              => 'required|array|min:1|max:15',
            'nombres_niños.*'            => 'required|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'apellidos_niños.*'          => 'required|regex:/^[a-zA-Z\sñÑáéíóúÁÉÍÓÚ]+$/u|min:2|max:50',
            'fechas_nacimiento_niños.*'  => 'required|date|before:today',
            'firma_base64'               => 'required|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'cedula.required'                    => 'El número de identificación es obligatorio.',
            'cedula.numeric'                     => 'El número de identificación debe ser numérico.',
            'cedula.digits_between'              => 'El número de identificación debe tener entre 7 y 8 dígitos.',
            'cedula.unique'                      => 'Este número de identificación ya está registrado.',
            'nombre.required'                    => 'El nombre del representante es obligatorio.',
            'nombre.regex'                       => 'El nombre del representante solo debe contener letras.',
            'nombre.min'                         => 'El nombre debe tener al menos 2 caracteres.',
            'nombre.max'                         => 'El nombre no debe exceder los 50 caracteres.',
            'apellido.required'                  => 'El apellido del representante es obligatorio.',
            'apellido.regex'                     => 'El apellido del representante solo debe contener letras.',
            'apellido.min'                       => 'El apellido debe tener al menos 2 caracteres.',
            'apellido.max'                       => 'El apellido no debe exceder los 50 caracteres.',
            'parentesco.required'                => 'Debe seleccionar su relación con los menores.',
            'parentesco.in'                      => 'Seleccione una opción de parentesco válida.',
            'correo.required'                    => 'El correo electrónico es obligatorio.',
            'correo.email'                       => 'Ingrese un formato de correo válido.',
            'correo.max'                         => 'El correo no debe exceder los 100 caracteres.',
            'telefono.required'                  => 'El número de teléfono es obligatorio.',
            'telefono.numeric'                   => 'El número de teléfono debe ser numérico.',
            'telefono.digits'                    => 'El número de teléfono debe tener exactamente 11 dígitos.',
            'fecha_nacimiento.required'          => 'Su fecha de nacimiento es obligatoria.',
            'fecha_nacimiento.before_or_equal'   => 'El representante debe ser mayor de edad (mínimo 18 años).',
            'nombres_niños.required'             => 'Debe registrar al menos un niño.',
            'nombres_niños.min'                  => 'Debe registrar al menos un niño.',
            'nombres_niños.max'                  => 'No puede registrar más de 15 niños por representante.',
            'nombres_niños.*.required'           => 'El nombre de cada niño es obligatorio.',
            'nombres_niños.*.regex'              => 'El nombre de los niños solo debe contener letras.',
            'apellidos_niños.*.required'         => 'El apellido de cada niño es obligatorio.',
            'apellidos_niños.*.regex'            => 'El apellido de los niños solo debe contener letras.',
            'fechas_nacimiento_niños.*.required' => 'La fecha de nacimiento de cada niño es obligatoria.',
            'fechas_nacimiento_niños.*.before'   => 'La fecha de nacimiento de los niños debe ser anterior a hoy.',
            'firma_base64.required'              => 'La firma digital es obligatoria. Por favor, firme el acuerdo.',
            'firma_base64.string'                => 'La firma digital enviada no tiene un formato válido.',
        ];
    }

    /**
     * Validación personalizada para garantizar integridad y concordancia de índices de niños.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $nombres = $this->input('nombres_niños', []);
            if (!is_array($nombres)) {
                return;
            }

            foreach ($nombres as $key => $nombre) {
                if (!$this->has("apellidos_niños.$key") || is_null($this->input("apellidos_niños.$key")) || trim($this->input("apellidos_niños.$key")) === '') {
                    $validator->errors()->add("apellidos_niños.$key", "El apellido para el niño en la posición " . ($key + 1) . " es obligatorio.");
                }
                if (!$this->has("fechas_nacimiento_niños.$key") || is_null($this->input("fechas_nacimiento_niños.$key")) || trim($this->input("fechas_nacimiento_niños.$key")) === '') {
                    $validator->errors()->add("fechas_nacimiento_niños.$key", "La fecha de nacimiento para el niño en la posición " . ($key + 1) . " es obligatoria.");
                }
            }
        });
    }
}
