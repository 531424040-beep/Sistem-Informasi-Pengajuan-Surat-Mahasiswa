<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuratHasilRequest extends FormRequest
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
            'file_surat' => ['required', 'file', 'mimes:pdf', 'max:2048'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'file_surat.required' => 'File surat hasil wajib diunggah.',
            'file_surat.file' => 'File surat hasil tidak valid.',
            'file_surat.mimes' => 'File surat hasil harus berformat PDF.',
            'file_surat.max' => 'Ukuran file surat hasil maksimal 2 MB.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'file_surat' => 'file surat hasil',
        ];
    }
}
