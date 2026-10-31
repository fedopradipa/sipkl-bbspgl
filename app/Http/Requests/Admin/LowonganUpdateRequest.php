<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LowonganUpdateRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  protected function prepareForValidation(): void
  {
    $this->merge([
      'is_fulltime' => $this->has('is_fulltime'),
      'is_open'     => $this->has('is_open'),
    ]);
  }

  public function rules(): array
  {
    return [
      'posisi'           => 'required|string|max:100',
      'deskripsi'        => 'nullable|string',
      'jurusan_terkait'  => 'nullable|string|max:60',

      'window_start_date' => 'required|date',
      'window_end_date'   => 'required|date|after_or_equal:window_start_date',
      'min_duration'      => 'required|integer|min:1',

      'is_fulltime'    => 'boolean',
      'is_open'        => 'boolean',
      'hari_kerja'     => 'nullable|array|required_if:is_fulltime,true',
      'hari_kerja.*'   => 'string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',

      'jam_buka_absen'     => 'required|date_format:H:i',
      'jam_masuk_standar'  => 'required|date_format:H:i|after_or_equal:jam_buka_absen',
      'jam_tutup_absen'    => 'required|date_format:H:i|after:jam_masuk_standar',
    ];
  }

  public function messages(): array
  {
    return [
      'required' => 'Kolom :attribute wajib diisi.',
      'date_format' => 'Format waktu :attribute harus Jam:Menit (Contoh: 08:00).',
      'jam_masuk_standar.after_or_equal' => 'Jam masuk standar tidak boleh lebih awal dari jam buka absen.',
      'jam_tutup_absen.after' => 'Jam tutup absen harus setelah jam masuk standar.',
      'hari_kerja.required_if' => 'Jika jadwal terikat dipilih, Anda wajib memilih hari kerja.',
    ];
  }
}
