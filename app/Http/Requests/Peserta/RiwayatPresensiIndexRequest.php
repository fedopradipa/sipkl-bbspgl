<?php

namespace App\Http\Requests\Peserta;

use Illuminate\Foundation\Http\FormRequest;

class RiwayatPresensiIndexRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'mulai'  => 'nullable|date',
      'sampai' => 'nullable|date|after_or_equal:mulai',
    ];
  }

  public function messages(): array
  {
    return [
      'mulai.date' => 'Format tanggal mulai tidak valid.',
      'sampai.date' => 'Format tanggal sampai tidak valid.',
      'sampai.after_or_equal' => 'Tanggal sampai tidak boleh sebelum tanggal mulai.',
    ];
  }
}
