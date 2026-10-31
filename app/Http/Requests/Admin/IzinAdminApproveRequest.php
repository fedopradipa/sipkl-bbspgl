<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class IzinAdminApproveRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  // approve tidak butuh input, tapi dibuat untuk "pisah request"
  public function rules(): array
  {
    return [];
  }
}
