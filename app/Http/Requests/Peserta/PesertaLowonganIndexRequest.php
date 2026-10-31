<?php

namespace App\Http\Requests\Peserta;

use Illuminate\Foundation\Http\FormRequest;

class PesertaLowonganIndexRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'keyword' => 'nullable|string|max:100',
    ];
  }

  public function messages(): array
  {
    return [
      'keyword.max' => 'Keyword maksimal 100 karakter.',
    ];
  }
}
