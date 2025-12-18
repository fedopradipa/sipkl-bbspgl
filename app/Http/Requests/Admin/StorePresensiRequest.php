<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StorePresensiRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'lamaran_id' => 'required|exists:lamarans,id',
      'tanggal'    => 'required|date',
      'status'     => 'required|in:hadir,terlambat,sakit,izin,alpha',
      'jam_masuk'  => 'nullable|required_if:status,hadir,terlambat',
      'jam_pulang' => 'nullable',
      'catatan_harian' => 'nullable|string|max:255',
    ];
  }
}
