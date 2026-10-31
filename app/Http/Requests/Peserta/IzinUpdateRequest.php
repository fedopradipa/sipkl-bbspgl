<?php

namespace App\Http\Requests\Peserta;

use Illuminate\Foundation\Http\FormRequest;
use Carbon\Carbon;

class IzinUpdateRequest extends FormRequest
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
            $fail('Tanggal wajib dipilih.');
            return;
          }

          $today = Carbon::today();

          foreach ($dates as $date) {
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
              $fail("Format tanggal salah.");
              return;
            }

            $d = Carbon::createFromFormat('Y-m-d', $date);
            if ($d->lt($today)) {
              $fail("Tanggal $date sudah terlewat. Pilih tanggal hari ini atau setelahnya.");
              return;
            }
          }
        },
      ],
    ];
  }
}
