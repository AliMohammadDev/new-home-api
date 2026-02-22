<?php

namespace App\Http\Requests\ProductImport;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductImportItemRequest extends FormRequest
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
      'product_import_id' => ['required', 'exists:product_imports,id'],
      'product_variant_id' => ['required', 'exists:product_variants,id'],
      'quantity' => ['required', 'integer', 'min:1'],
      'price' => ['required', 'numeric', 'min:0'],
      'shipping_price' => ['required', 'numeric', 'min:0'],
      'discount' => ['nullable', 'numeric', 'min:0'],
      'expected_arrival' => ['nullable', 'date'],
    ];
  }
}