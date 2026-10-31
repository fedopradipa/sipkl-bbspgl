<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LowonganRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true; // Ubah jadi true agar bisa dipakai
  }

  /**
   * Persiapan data sebelum validasi (Normalisasi Checkbox & Nullable)
   */
  protected function prepareForValidation()
  {
    $this->merge([
      // Ubah "on" dari checkbox menjadi boolean true/false
      'is_fulltime' => $this->has('is_fulltime'),
      'is_open'     => $this->has('is_open'),
    ]);
  }

  public function rules(): array
  {
    return [
      'posisi'             => 'required|string|max:255',
      'divisi'            => 'required|string|max:255',
      'deskripsi'         => 'nullable|string',
      'jurusan_terkait'   => 'nullable|string',
      'fasilitas'         => 'nullable|string',

      // Validasi Tanggal
      'window_start_date' => 'required|date',
      'window_end_date'   => 'required|date|after_or_equal:window_start_date',
      'min_duration'      => 'required|integer|min:1',

      // Validasi Jam
      'jam_masuk_standar'  => 'required|date_format:H:i',

      // Validasi Boolean & JSON Array
      'is_fulltime'       => 'boolean',
      'is_open'           => 'boolean',

      // LOGIKA WORKING DAYS (Sesuai diskusi sebelumnya)
      // Jika is_fulltime true, maka hari_kerja wajib ada dan berupa array
      'hari_kerja'      => 'nullable|array|required_if:is_fulltime,true',
      'hari_kerja.*'    => 'string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
    ];
  }

  public function messages(): array
  {
    return [
      'required' => 'Kolom :attribute wajib diisi.',
      'date_format' => 'Format waktu :attribute harus Jam:Menit (Contoh: 08:00).',
      'hari_kerja.required_if' => 'Jika jadwal terikat dipilih, Anda wajib memilih hari kerja.',
    ];
  }
}
