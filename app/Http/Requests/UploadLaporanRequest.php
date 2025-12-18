<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadLaporanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id'           => 'required|exists:lamarans,id',
            'file_laporan' => 'required|file|mimes:pdf|max:5120', // Max 5MB, PDF Only
        ];
    }

    public function messages(): array
    {
        return [
            'file_laporan.required' => 'Dokumen laporan wajib diunggah.',
            'file_laporan.mimes'    => 'Format laporan harus PDF.',
            'file_laporan.max'      => 'Ukuran file maksimal 5MB.',
        ];
    }
}
