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
      'city' => ['required', 'string', 'max:150'],
      'address' => ['required', 'string', 'max:150'],
      'cart_id'=> ['required', 'string','exists:carts,id'],
    ];
  }
}
