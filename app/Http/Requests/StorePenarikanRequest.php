<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StorePenarikanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();
        if (in_array($user->level, ['admin', 'badkom_pusat'])) {
            return true;
        }

        if ($user->level === 'badkom_wilayah') {
            $utd = \App\Models\Utd::with('pjutd')->find($this->utd_id);
            if ($utd) {
                return $utd->pjutd->badkom_id === $user->badkom_id;
            }
        }
        
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'utd_id' => 'required|exists:utds,id',
            'alasan' => 'required|string',
            'tanggal_penarikan' => 'required|date',
        ];
    }
}
