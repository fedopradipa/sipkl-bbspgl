<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LamaranRejectRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'catatan_admin' => 'required|string|max:500',
    ];
  }

  public function messages(): array
  {
    return [
      'catatan_admin.required' => 'Catatan penolakan wajib diisi.',
      'catatan_admin.max'      => 'Catatan penolakan maksimal 500 karakter.',
    ];
  }
}
