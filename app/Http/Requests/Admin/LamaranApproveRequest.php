<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LamaranApproveRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'pembimbing_id'     => 'required|exists:pembimbings,id',
      'tgl_mulai_resmi'   => 'required|date',
      'tgl_selesai_resmi' => 'required|date|after_or_equal:tgl_mulai_resmi',
      'hari_kerja_resmi'  => 'required|array',
      'request_mode'      => 'nullable|string',
    ];
  }

  public function messages(): array
  {
    return [
      'pembimbing_id.required' => 'Pembimbing wajib dipilih.',
      'pembimbing_id.exists'   => 'Pembimbing tidak valid.',
      'tgl_mulai_resmi.required' => 'Tanggal mulai resmi wajib diisi.',
      'tgl_selesai_resmi.required' => 'Tanggal selesai resmi wajib diisi.',
      'tgl_selesai_resmi.after_or_equal' => 'Tanggal selesai tidak boleh sebelum tanggal mulai.',
      'hari_kerja_resmi.required' => 'Hari kerja resmi wajib dipilih.',
      'hari_kerja_resmi.array'    => 'Format hari kerja resmi tidak valid.',
    ];
  }
}
