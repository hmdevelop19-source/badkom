<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePjutdRequest extends FormRequest
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
            'kode_lembaga' => 'required|unique:pjutds',
            'nama_pjutd' => 'required|string',
            'nama_madrasah' => 'nullable|string',
            'yayasan' => 'nullable|string',
            'no_hp' => 'nullable|string',
            'badkom_id' => 'required|exists:badkoms,id',
            'id_prov' => 'nullable|integer',
            'id_kab' => 'nullable|integer',
            'id_kec' => 'nullable|integer',
            'id_kel' => 'nullable|integer',
            'alamat' => 'nullable|string',
        ];
    }
}
