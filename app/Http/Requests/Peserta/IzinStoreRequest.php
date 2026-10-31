<?php

namespace App\Http\Requests\Peserta;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class IzinStoreRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'jenis_izin' => 'required|in:sakit,izin',
      'alasan'     => 'required|string|min:5|max:500',
      'bukti_lampiran' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',
      'tanggal_izin' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          $dates = explode(', ', $value);

          if (empty($dates) || trim($value) === '') {
            $fail('Anda wajib memilih minimal satu tanggal pada kalender.');
            return;
          }

          $today = Carbon::today();

          foreach ($dates as $date) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
              $fail("Format tanggal salah ($date). Refresh halaman.");
              return;
            }

            $d = Carbon::createFromFormat('Y-m-d', $date);
            if ($d->lt($today)) {
              $fail("Tanggal $date sudah terlewat. Pilih tanggal hari ini atau setelahnya.");
              return;
            }
          }

          if (count($dates) > 14) {
            $fail('Maksimal pengajuan izin adalah 14 hari sekaligus.');
          }
        },
      ],
    ];
  }

  public function messages(): array
  {
    return [
      'jenis_izin.required' => 'Silakan pilih jenis izin.',
      'alasan.required'     => 'Mohon isi alasan pengajuan izin.',
      'bukti_lampiran.max'  => 'Ukuran file bukti maksimal 5MB.',
      'bukti_lampiran.mimes' => 'File bukti harus berupa Gambar atau PDF.',
    ];
  }
}
