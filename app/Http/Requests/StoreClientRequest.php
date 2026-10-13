<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientRequest extends FormRequest
{
  public function authorize(): bool
  {
    return true;
  }

  /** @return array<string, list<string>> */
  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:255'],
      'phone' => ['required', 'string', 'max:100'],
      'email' => ['nullable', 'string', 'email', 'max:255'],
      'address' => ['nullable', 'string'],
    ];
  }
}
