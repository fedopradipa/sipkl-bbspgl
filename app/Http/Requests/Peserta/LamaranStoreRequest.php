<?php

namespace App\Http\Requests\Peserta;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class LamaranStoreRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    $lowongan = $this->route('lowongan'); // Route model binding

    return [
      'tgl_mulai_usulan' => [
        'required',
        'date',
        'after_or_equal:' . $lowongan->window_start_date,
      ],
      'tgl_selesai_usulan' => [
        'required',
        'date',
        'after:tgl_mulai_usulan',
      ],
      'hari_kerja' => $lowongan->is_fulltime ? 'nullable' : 'required|array|min:1',
      'alasan_hari_kerja' => $lowongan->is_fulltime ? 'nullable' : 'required|string',
      'file_surat_pengantar' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ];
  }

  public function messages(): array
  {
    return [
      'tgl_mulai_usulan.after_or_equal' => 'Tanggal mulai tidak boleh sebelum periode lowongan.',
      'tgl_selesai_usulan.after'        => 'Tanggal selesai harus setelah tanggal mulai.',
      'hari_kerja.required'             => 'Anda wajib memilih minimal 1 hari kerja.',
      'file_surat_pengantar.required'   => 'Anda wajib menyertakan dokumen.',
    ];
  }

  // Validasi tambahan: minimal durasi (min_duration)
  public function withValidator($validator): void
  {
    $validator->after(function ($validator) {
      $lowongan = $this->route('lowongan');

      $tglMulai   = $this->input('tgl_mulai_usulan');
      $tglSelesai = $this->input('tgl_selesai_usulan');

      if (!$lowongan || !$tglMulai || !$tglSelesai) return;

      $start = Carbon::parse($tglMulai);
      $end   = Carbon::parse($tglSelesai);

      if ($start->floatDiffInMonths($end) < (int) $lowongan->min_duration) {
        $validator->errors()->add(
          'min_duration',
          "Durasi PKL kurang. Minimal {$lowongan->min_duration} bulan."
        );
      }
    });
  }
}
