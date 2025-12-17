<?php

namespace App\Http\Requests\Checkout;

use Illuminate\Foundation\Http\FormRequest;

class CreateCheckoutRequest extends FormRequest
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
      'first_name' => ['required', 'string', 'max:150'],
      'last_name' => ['required', 'string', 'max:150'],
      'email' => ['required', 'email', 'max:255'],
      'phone' => ['required', 'string', 'max:20'],
      'country' => ['required', 'string', 'max:150'],
      'city' => ['required', 'string', 'max:150'],
      'street' => ['nullable', 'string', 'max:150'],
      'floor' => ['nullable', 'string', 'max:50'],
      'postal_code' => ['nullable', 'string', 'max:20'],
      'additional_information' => ['nullable', 'string', 'max:500'],
      'cart_id' => ['required', 'exists:carts,id'],
    ];
  }
}
