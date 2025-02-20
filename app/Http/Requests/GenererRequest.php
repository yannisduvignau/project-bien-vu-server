<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenererRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'type' => 'required|string|max:255',
            'surface' => 'required|numeric|min:1',
            'pieces' => 'required|integer|min:1',
            'ville' => 'required|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'type.required' => __('validation.generer.type.required'),
            'type.string' => __('validation.generer.type.string'),
            'type.max' => __('validation.generer.type.max'),

            'surface.required' => __('validation.generer.surface.required'),
            'surface.numeric' => __('validation.generer.surface.numeric'),
            'surface.min' => __('validation.generer.surface.min'),

            'pieces.required' => __('validation.generer.pieces.required'),
            'pieces.integer' => __('validation.generer.pieces.integer'),
            'pieces.min' => __('validation.generer.pieces.min'),

            'ville.required' => __('validation.generer.ville.required'),
            'ville.string' => __('validation.generer.ville.string'),
            'ville.max' => __('validation.generer.ville.max'),
        ];
    }
}
