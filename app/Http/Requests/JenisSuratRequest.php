<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class JenisSuratRequest extends FormRequest
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
        $jenisSurat = $this->route('jenisSurat');

        return [
            'nama_surat' => [
                'required',
                'string',
                'max:255',
                Rule::unique('jenis_surats', 'nama_surat')->ignore($jenisSurat?->id),
            ],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', 'boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nama_surat.required' => 'Nama surat wajib diisi.',
            'nama_surat.unique' => 'Nama surat tersebut sudah terdaftar.',
            'deskripsi.max' => 'Deskripsi maksimal 1000 karakter.',
            'status.required' => 'Status jenis surat wajib dipilih.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'nama_surat' => 'nama surat',
            'deskripsi' => 'deskripsi',
            'status' => 'status',
        ];
    }
}
