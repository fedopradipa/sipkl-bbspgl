<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PresensiUpdateAdminRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'status'         => 'required|in:hadir,terlambat,sakit,izin,alpha',
      'jam_masuk'      => 'nullable|required_if:status,hadir,terlambat|date_format:H:i',
      'catatan_harian' => 'nullable|string|max:255',
    ];
  }

  public function messages(): array
  {
    return [
      'status.required'      => 'Status wajib dipilih.',
      'status.in'            => 'Status tidak valid.',
      'jam_masuk.required_if' => 'Jam masuk wajib diisi untuk status hadir/terlambat.',
      'jam_masuk.date_format' => 'Format jam masuk harus Jam:Menit (Contoh: 08:00).',
      'catatan_harian.max'   => 'Catatan maksimal 255 karakter.',
    ];
  }
}
