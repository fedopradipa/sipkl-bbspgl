<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class IzinAdminIndexRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'status' => 'nullable|in:all,pending,disetujui,ditolak',
      'search' => 'nullable|string|max:100',
    ];
  }

  public function messages(): array
  {
    return [
      'status.in'    => 'Filter status tidak valid.',
      'search.string' => 'Kolom pencarian harus berupa teks.',
      'search.max'   => 'Kolom pencarian maksimal 100 karakter.',
    ];
  }
}
