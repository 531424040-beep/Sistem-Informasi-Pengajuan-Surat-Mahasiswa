<?php

namespace App\Http\Requests;

use App\Models\JenisSurat;
use Illuminate\Foundation\Http\FormRequest;

class StorePengajuanRequest extends FormRequest
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
        $rules = [
            'jenis_surat_id' => ['required', 'integer', 'exists:jenis_surats,id'],
            'keperluan' => ['required', 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:1000'],
            'dokumen' => ['nullable', 'array'],
        ];

        $jenisSurat = JenisSurat::with('persyaratans')->find($this->input('jenis_surat_id'));

        if ($jenisSurat) {
            $this->addPersyaratanRules($rules, $jenisSurat);
            $this->addExtraFieldRules($rules, $jenisSurat);
        }

        return $rules;
    }

    /**
     * @param  array<string, mixed>  $rules
     */
    private function addPersyaratanRules(array &$rules, JenisSurat $jenisSurat): void
    {
        foreach ($jenisSurat->persyaratans as $persyaratan) {
            $rule = ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'];

            if ($persyaratan->wajib) {
                array_unshift($rule, 'required');
            }

            $rules['dokumen.'.$persyaratan->id] = $rule;
        }
    }

    /**
     * Field tambahan khusus Surat Pengantar Penelitian.
     *
     * @param  array<string, mixed>  $rules
     */
    private function addExtraFieldRules(array &$rules, JenisSurat $jenisSurat): void
    {
        if ($jenisSurat->nama_surat === 'Surat Pengantar Penelitian') {
            $rules['judul_penelitian'] = ['required', 'string', 'max:255'];
            $rules['instansi_tujuan'] = ['required', 'string', 'max:255'];
            $rules['alamat_instansi'] = ['required', 'string', 'max:255'];
            $rules['tujuan_penelitian'] = ['required', 'string', 'max:1000'];
        }
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'jenis_surat_id.required' => 'Jenis surat wajib dipilih.',
            'jenis_surat_id.exists' => 'Jenis surat tidak ditemukan.',
            'keperluan.required' => 'Keperluan surat wajib diisi.',
            'keperluan.max' => 'Keperluan surat maksimal 255 karakter.',
            'keterangan.max' => 'Keterangan tambahan maksimal 1000 karakter.',
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
            'jenis_surat_id' => 'jenis surat',
            'keperluan' => 'keperluan surat',
            'keterangan' => 'keterangan tambahan',
            'judul_penelitian' => 'judul penelitian',
            'instansi_tujuan' => 'instansi tujuan',
            'alamat_instansi' => 'alamat instansi',
            'tujuan_penelitian' => 'tujuan penelitian',
        ];
    }

    /**
     * Validasi tambahan: jenis surat harus aktif dan dokumen wajib tersedia.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $jenisSurat = JenisSurat::find($this->input('jenis_surat_id'));

            if (! $jenisSurat) {
                return;
            }

            if (! $jenisSurat->isActive()) {
                $validator->errors()->add('jenis_surat_id', 'Jenis surat ini tidak aktif dan tidak dapat diajukan.');
            }

            foreach ($jenisSurat->persyaratans as $persyaratan) {
                if ($persyaratan->wajib && ! $this->hasFile('dokumen.'.$persyaratan->id)) {
                    $validator->errors()->add(
                        'dokumen.'.$persyaratan->id,
                        'Dokumen "'.$persyaratan->nama_persyaratan.'" wajib diunggah.'
                    );
                }
            }
        });
    }
}
