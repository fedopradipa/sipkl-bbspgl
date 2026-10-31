<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class StoreLamaranRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true; // Authorization logic bisa ditambahkan disini
  }

  /**
   * Get the validation rules that apply to the request.
   */
  public function rules(): array
  {
    $lowongan = $this->route('lowongan');

    return [
      'tgl_mulai_usulan'   => [
        'required',
        'date',
        'after_or_equal:' . $lowongan->window_start_date
      ],
      'tgl_selesai_usulan' => 'required|date|after:tgl_mulai_usulan',
      'hari_kerja'         => $lowongan->is_fulltime ? 'nullable' : 'required|array|min:1',
      'alasan_hari_kerja'  => $lowongan->is_fulltime ? 'nullable' : 'required|string',
      'file_surat_pengantar' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ];
  }

  /**
   * Get custom messages for validator errors.
   */
  public function messages(): array
  {
    return [
      'tgl_mulai_usulan.after_or_equal' => 'Tanggal mulai tidak boleh sebelum periode lowongan.',
      'tgl_selesai_usulan.after'        => 'Tanggal selesai harus setelah tanggal mulai.',
      'hari_kerja.required'             => 'Anda wajib memilih minimal 1 hari kerja.',
      'file_surat_pengantar.required'   => 'Anda wajib menyertakan dokumen.',
    ];
  }

  /**
   * Custom validation logic
   */
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      // Validasi durasi minimal
      if (!$this->hasValidDuration()) {
        $validator->errors()->add(
          'min_duration',
          "Durasi PKL kurang. Minimal {$this->route('lowongan')->min_duration} bulan."
        );
      }
    });
  }

  /**
   * Check if duration meets minimum requirement
   */
  private function hasValidDuration(): bool
  {
    $lowongan = $this->route('lowongan');
    $start = Carbon::parse($this->tgl_mulai_usulan);
    $end   = Carbon::parse($this->tgl_selesai_usulan);

    return $start->floatDiffInMonths($end) >= $lowongan->min_duration;
  }
}
