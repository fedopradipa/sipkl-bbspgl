<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PesertaAktifIndexRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true; // sesuaikan jika ada role/policy
  }

  public function rules(): array
  {
    return [
      'search' => 'nullable|string|max:100',
    ];
  }

  public function messages(): array
  {
    return [
      'search.string' => 'Kolom pencarian harus berupa teks.',
      'search.max'    => 'Kolom pencarian maksimal 100 karakter.',
    ];
  }
}
