<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreIzinRequest extends FormRequest
{
  /**
   * Tentukan apakah user boleh melakukan request ini.
   */
  public function authorize(): bool
  {
    return true; // Autorisasi sudah dicek di Controller/Middleware
  }

  /**
   * Aturan validasi.
   */
  public function rules(): array
  {
    return [
      'jenis_izin' => 'required|in:sakit,izin',

      'alasan'     => 'required|string|min:5|max:500',

      // Validasi File Bukti (Opsional tapi dibatasi)
      // Max: 5120 KB = 5 MB (Cukup untuk PDF/Foto HP modern)
      'bukti_foto' => 'nullable|file|max:5120|mimes:jpg,jpeg,png,pdf',

      /**
       * VALIDASI KHUSUS TANGGAL (Multi-Date String)
       * Input dari Flatpickr bentuknya string: "2025-10-01, 2025-10-03, 2025-10-04"
       */
      'tanggal_izin' => [
        'required',
        'string',
        function ($attribute, $value, $fail) {
          // 1. Pecah string berdasarkan koma dan spasi (delimiter default flatpickr)
          $dates = explode(', ', $value);

          // 2. Cek apakah kosong
          if (empty($dates) || trim($value) === '') {
            $fail('Anda wajib memilih minimal satu tanggal pada kalender.');
            return;
          }

          // 3. Validasi format setiap tanggal (harus Y-m-d)
          foreach ($dates as $date) {
            // Regex sederhana untuk YYYY-MM-DD
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
              $fail("Format tanggal salah ($date). Mohon refresh halaman dan pilih ulang.");
              return;
            }
          }

          // 4. (Opsional) Validasi Logic Tambahan
          // Misal: Tidak boleh mengajukan izin lebih dari 14 hari sekaligus
          if (count($dates) > 14) {
            $fail('Maksimal pengajuan izin adalah 14 hari dalam satu kali pengajuan.');
          }
        },
      ],
    ];
  }

  /**
   * Pesan error custom (Bahasa Indonesia).
   */
  public function messages(): array
  {
    return [
      'jenis_izin.required'   => 'Silakan pilih jenis izin (Sakit atau Kepentingan Lain).',
      'jenis_izin.in'         => 'Jenis izin tidak valid.',

      'tanggal_izin.required' => 'Tanggal izin wajib dipilih.',

      'alasan.required'       => 'Mohon isi alasan pengajuan izin.',
      'alasan.min'            => 'Alasan terlalu singkat, mohon jelaskan lebih detail.',

      'bukti_foto.max'        => 'Ukuran file bukti maksimal 5MB.',
      'bukti_foto.mimes'      => 'File bukti harus berupa Gambar (JPG, PNG) atau PDF.',
    ];
  }
}
