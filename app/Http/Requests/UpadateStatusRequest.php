<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStatusRequest extends FormRequest
{
  public function authorize(): bool
  {
    // Only admin can update status
    return auth()->user()->isAdmin() || auth()->user()->isSuperadmin();
  }

  public function rules(): array
  {
    return [
      'status'  => 'required|in:pending,diterima,ditolak,selesai,berhenti',
      'catatan' => 'nullable|string|max:500',
      'kategori' => 'nullable|string',
      'catatan_detail' => 'nullable|string|max:500',
    ];
  }

  public function messages(): array
  {
    return [
      'status.required' => 'Status wajib dipilih.',
      'status.in' => 'Status tidak valid.',
    ];
  }
}
