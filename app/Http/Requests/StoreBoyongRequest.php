<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreBoyongRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        return in_array($user->level, ['admin', 'badkom_pusat']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nis' => 'required|string|exists:santris,nis',
            'tahun_mondok' => 'nullable|string',
            'tahun_tugas' => 'nullable|string',
            'keterangan' => 'nullable|string'
        ];
    }
    
    public function messages(): array
    {
        return [
            'nis.exists' => 'Santri dengan NIS tersebut tidak ditemukan.'
        ];
    }
}
