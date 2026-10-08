<?php

namespace App\Http\Requests;

use App\Enums\PengajuanStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VerifikasiPengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'action' => ['required', Rule::in(['setujui', 'perbaikan', 'tolak'])],
            'catatan' => ['nullable', 'string', 'max:1000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'action.required' => 'Tindakan verifikasi wajib dipilih.',
            'action.in' => 'Tindakan verifikasi tidak dikenal.',
            'catatan.max' => 'Catatan maksimal 1000 karakter.',
        ];
    }

    /**
     * Admin wajib memberikan catatan/alasan saat meminta perbaikan atau menolak.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if (in_array($this->input('action'), ['perbaikan', 'tolak'], true)
                && trim((string) $this->input('catatan')) === '') {
                $label = $this->input('action') === 'perbaikan' ? 'perbaikan' : 'penolakan';
                $validator->errors()->add('catatan', 'Alasan '.$label.' wajib diisi.');
            }
        });
    }

    public function targetStatus(): PengajuanStatus
    {
        return match ($this->input('action')) {
            'setujui' => PengajuanStatus::Diproses,
            'perbaikan' => PengajuanStatus::PerluPerbaikan,
            'tolak' => PengajuanStatus::Ditolak,
        };
    }
}
