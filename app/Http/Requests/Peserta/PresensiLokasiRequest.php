<?php

namespace App\Http\Requests\Peserta;

use Illuminate\Foundation\Http\FormRequest;

class PresensiLokasiRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'latitude'  => 'required|numeric',
      'longitude' => 'required|numeric',
    ];
  }

  public function messages(): array
  {
    return [
      'latitude.required' => 'Lokasi tidak terdeteksi. Pastikan GPS aktif.',
    ];
  }
}
