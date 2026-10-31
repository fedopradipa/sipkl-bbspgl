<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PembimbingStoreRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true; // sesuaikan jika pakai policy/role
  }

  protected function prepareForValidation(): void
  {
    $this->merge([
      'is_active' => $this->has('is_active'),
    ]);
  }

  public function rules(): array
  {
    return [
      'nama'      => 'required|string|max:255',
      'jabatan'   => 'required|string|max:255',
      'no_hp'     => 'nullable|string|max:20',
      'is_active' => 'boolean',
    ];
  }

  public function messages(): array
  {
    return [
      'required' => 'Kolom :attribute wajib diisi.',
      'max'      => 'Kolom :attribute maksimal :max karakter.',
      'string'   => 'Kolom :attribute harus berupa teks.',
    ];
  }
}
