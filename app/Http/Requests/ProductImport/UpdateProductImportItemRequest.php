<?php

namespace App\Http\Requests\ProductImport;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImportItemRequest extends FormRequest
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
      'product_import_id' => ['sometimes', 'exists:product_imports,id'],
      'product_variant_id' => ['sometimes', 'exists:product_variants,id'],
      'quantity' => ['sometimes', 'integer', 'min:1'],
      'price' => ['sometimes', 'numeric', 'min:0'],
      'shipping_price' => ['sometimes', 'numeric', 'min:0'],
      'discount' => ['nullable', 'numeric', 'min:0'],
      'expected_arrival' => ['nullable', 'date'],
    ];
  }
}
