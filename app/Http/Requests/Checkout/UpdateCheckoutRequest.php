<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCheckoutRequest extends FormRequest
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
      'first_name' => ['sometimes', 'string', 'max:150'],
      'last_name' => ['sometimes', 'string', 'max:150'],
      'email' => ['sometimes', 'email', 'max:255'],
      'phone' => ['sometimes', 'string', 'max:20'],
      'country' => ['sometimes', 'string', 'max:150'],
      'city' => ['sometimes', 'string', 'max:150'],
      'street' => ['sometimes', 'string', 'max:150'],
      'floor' => ['sometimes', 'nullable', 'string', 'max:50'],
      'postal_code' => ['sometimes', 'nullable', 'string', 'max:20'],
      'additional_information' => ['sometimes', 'nullable', 'string', 'max:500'],
    ];
  }
}