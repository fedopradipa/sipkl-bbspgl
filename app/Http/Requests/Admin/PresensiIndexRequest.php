<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PresensiIndexRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'tanggal'     => 'nullable|date',
      'lowongan_id' => 'nullable|exists:lowongans,id',
      'status'      => 'nullable|in:hadir,terlambat,sakit,izin,alpha,belum_absen',
      'keyword'     => 'nullable|string|max:100',
    ];
  }

  public function messages(): array
  {
    return [
      'tanggal.date'        => 'Format tanggal tidak valid.',
      'lowongan_id.exists'  => 'Lowongan yang dipilih tidak valid.',
      'status.in'           => 'Status yang dipilih tidak valid.',
      'keyword.string'      => 'Keyword harus berupa teks.',
      'keyword.max'         => 'Keyword maksimal 100 karakter.',
    ];
  }
}
