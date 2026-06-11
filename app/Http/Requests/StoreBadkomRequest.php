<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBadkomRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'kode_badkom' => 'required|unique:badkoms',
            'nama_pj' => 'required|string',
            'email' => 'nullable|email',
            'wilayah_koordinasi' => 'required|string',
            'alamat' => 'nullable|string',
            'no_hp' => 'nullable|string',
        ];
    }
}
