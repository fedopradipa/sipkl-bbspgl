<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIzinRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'jenis_izin'  => 'required|in:sakit,izin',
      'tgl_mulai'   => 'required|date',
      // Tanggal selesai minimal hari ini atau setelah tanggal mulai
      'tgl_selesai' => 'required|date|after_or_equal:tgl_mulai',
      'alasan'      => 'required|string|max:500',
      // File bukti opsional, tapi jika ada harus valid
      'bukti_foto'  => 'nullable|file|max:2048|mimes:jpg,jpeg,png,pdf',
    ];
  }

  public function messages(): array
  {
    return [
      'tgl_selesai.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
      'bukti_foto.max'             => 'Ukuran file maksimal 2MB.',
      'bukti_foto.mimes'           => 'File harus berupa JPG, PNG, atau PDF.',
    ];
  }
}
