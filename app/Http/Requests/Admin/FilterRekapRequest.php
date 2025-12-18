<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FilterRekapRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation()
  {
    // Default ke Bulan & Tahun saat ini jika kosong
    $this->merge([
      'bulan' => $this->bulan ?? date('n'),
      'tahun' => $this->tahun ?? date('Y'),
    ]);
  }

  public function rules(): array
  {
    return [
      'bulan'       => 'required|integer|min:1|max:12',
      'tahun'       => 'required|integer|min:2020|max:' . (date('Y') + 1),
      'lowongan_id' => 'nullable|exists:lowongans,id',
    ];
  }
}
