<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePengajuanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isMahasiswa() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $jenisSurat = $this->route('pengajuan')?->jenisSurat;

        $rules = [
            'keperluan' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
        ];

        if ($jenisSurat && $jenisSurat->nama_surat === 'Surat Pengantar Penelitian') {
            $rules['judul_penelitian'] = ['required', 'string', 'max:255'];
            $rules['instansi_tujuan'] = ['required', 'string', 'max:255'];
            $rules['alamat_instansi'] = ['required', 'string', 'max:255'];
            $rules['tujuan_penelitian'] = ['required', 'string', 'max:1000'];
        }

        if ($jenisSurat) {
            foreach ($jenisSurat->persyaratans as $persyaratan) {
                $rules['dokumen.'.$persyaratan->id] = [
                    'nullable',
                    'file',
                    'mimes:pdf,jpg,jpeg,png',
                    'max:2048',
                ];
            }
        }

        return $rules;
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'keperluan.required' => 'Keperluan surat wajib diisi.',
            'keperluan.max' => 'Keperluan surat maksimal 255 karakter.',
            'judul_penelitian.required' => 'Judul penelitian wajib diisi.',
            'instansi_tujuan.required' => 'Instansi tujuan wajib diisi.',
            'alamat_instansi.required' => 'Alamat instansi wajib diisi.',
            'tujuan_penelitian.required' => 'Tujuan penelitian wajib diisi.',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'keperluan' => 'keperluan surat',
            'keterangan' => 'keterangan tambahan',
            'judul_penelitian' => 'judul penelitian',
            'instansi_tujuan' => 'instansi tujuan',
            'alamat_instansi' => 'alamat instansi',
            'tujuan_penelitian' => 'tujuan penelitian',
        ];
    }
}
