<?php

namespace App\Http\Requests\ProductPackage;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductVariantsPackage extends FormRequest
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
      'quantity' => [
        'required',
        'integer',
        'min:1',
        Rule::unique('product_variant_packages')
          ->where(
            fn($q) =>
            $q->where('product_variant_id', $this->product_variant_id)
          )
          ->ignore($this->package),
      ],

      'price' => [
        'required',
        'numeric',
        'min:0',
      ],
    ];
  }
}
