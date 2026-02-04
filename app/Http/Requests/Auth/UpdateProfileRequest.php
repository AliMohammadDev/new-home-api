<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name' => ['sometimes', 'string', 'max:255'],
      'email' => ['sometimes', 'email', 'unique:users,email,' . auth()->id()],
      'password' => ['nullable', 'confirmed', 'min:6'],

      // for checkouts
      'first_name' => ['sometimes', 'string', 'max:150'],
      'last_name' => ['sometimes', 'string', 'max:150'],
      'phone' => ['sometimes', 'string', 'max:20'],
      'country' => ['sometimes', 'string', 'max:150'],
      'city' => ['sometimes', 'string', 'max:150'],
      'street' => ['nullable', 'string', 'max:150'],
      'postal_code' => ['nullable', 'string', 'max:20'],
      'additional_information' => ['nullable', 'string', 'max:500'],
    ];
  }
}
