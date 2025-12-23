<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('form'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'form_id' => ['required', 'string', 'max:255', 'unique:forms,form_id,' . $this->route('form')->id],
            'version' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'definition' => ['nullable', 'array'],
            'settings' => ['nullable', 'array'],
        ];
    }
}