<?php

namespace App\Http\Requests\Peserta;

use Illuminate\Foundation\Http\FormRequest;

class PresensiCheckInRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  // Tidak ada input dari form check-in, jadi kosong
  public function rules(): array
  {
    return [];
  }
}
