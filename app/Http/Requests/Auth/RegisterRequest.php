<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'name'        => 'required|string|max:255',
      'email'       => 'required|string|email|max:255|unique:users',
      'password'    => 'required|string|min:8|confirmed',

      'nim_nis'     => 'required|string|max:50',
      'institusi'   => 'required|string|max:100',
      'jurusan'     => 'required|string|max:100',
      'no_hp'       => 'required|string|max:20',
      'alamat'      => 'required|string',
      'foto_profil' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ];
  }

  public function messages(): array
  {
    return [
      'email.unique'         => 'Email ini sudah terdaftar.',
      'password.confirmed'   => 'Konfirmasi password tidak cocok.',
      'password.min'         => 'Password minimal 8 karakter.',
    ];
  }
}
