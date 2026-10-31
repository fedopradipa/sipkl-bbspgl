<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LamaranUpdateStatusRequest extends FormRequest
{
  public function authorize(): bool
  {
    // Biar sama seperti sebelumnya: controller tetap cek isAdmin()
    return true;
  }

  public function rules(): array
  {
    return [
      'status'         => 'required|in:pending,diterima,ditolak,selesai,berhenti',
      'catatan'        => 'nullable|string|max:500',
      'catatan_detail' => 'required_if:status,berhenti|nullable|string|max:500',
    ];
  }

  public function messages(): array
  {
    return [
      'status.required' => 'Status wajib dipilih.',
      'status.in'       => 'Status tidak valid.',
      'catatan.max'     => 'Catatan maksimal 500 karakter.',
      'catatan_detail.required_if' => 'Detail alasan wajib diisi jika status berhenti.',
      'catatan_detail.max' => 'Detail alasan maksimal 500 karakter.',
    ];
  }
}
