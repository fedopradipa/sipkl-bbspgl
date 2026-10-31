<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class AlumniExportRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'status'  => 'nullable|in:all,selesai,berhenti',
      'keyword' => 'nullable|string|max:100',
    ];
  }

  public function messages(): array
  {
    return [
      'status.in'      => 'Filter status tidak valid.',
      'keyword.string' => 'Keyword harus berupa teks.',
      'keyword.max'    => 'Keyword maksimal 100 karakter.',
    ];
  }
}
