<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PersyaratanRequest extends FormRequest
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
            'nama_persyaratan' => ['required', 'string', 'max:255'],
            'wajib' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_persyaratan.required' => 'Nama persyaratan wajib diisi.',
            'wajib.required' => 'Status wajib/tidak wajib harus dipilih.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_persyaratan' => 'nama persyaratan',
            'wajib' => 'keterangan wajib',
        ];
    }
}
