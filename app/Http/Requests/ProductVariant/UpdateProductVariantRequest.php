<?php

namespace App\Http\Requests\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVariantRequest extends FormRequest
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
      'product_id' => ['sometimes', 'exists:products,id'],
      'color_id' => ['sometimes', 'exists:colors,id'],
      'size_id' => ['sometimes', 'exists:sizes,id'],
      'material_id' => ['sometimes', 'exists:materials,id'],
      'price' => ['sometimes', 'numeric', 'min:0'],
      'discount' => ['sometimes', 'numeric', 'min:0'],
      'stock_quantity' => ['sometimes', 'integer', 'min:0'],

      'images' => ['nullable', 'array'],
      'images.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:10240'], // 10MB
    ];
  }
}