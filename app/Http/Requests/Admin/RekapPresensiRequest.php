<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RekapPresensiRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true; // sesuaikan kalau ada policy/role
  }

  public function rules(): array
  {
    return [
      'bulan'       => 'nullable|integer|min:1|max:12',
      'tahun'       => 'nullable|integer|min:2020|max:' . (date('Y') + 1),
      'lowongan_id' => 'nullable|exists:lowongans,id',
    ];
  }

  public function messages(): array
  {
    return [
      'bulan.integer'      => 'Bulan harus berupa angka.',
      'bulan.min'          => 'Bulan minimal 1.',
      'bulan.max'          => 'Bulan maksimal 12.',
      'tahun.integer'      => 'Tahun harus berupa angka.',
      'tahun.min'          => 'Tahun minimal 2020.',
      'tahun.max'          => 'Tahun terlalu besar.',
      'lowongan_id.exists' => 'Lowongan yang dipilih tidak valid.',
    ];
  }
}
