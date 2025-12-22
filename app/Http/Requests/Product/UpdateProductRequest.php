<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
      'body' => ['sometimes', 'string', 'max:1000'],
      'category_id' => ['sometimes', 'exists:categories,id'],
      'image' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:3072'],
      'price' => ['sometimes', 'numeric', 'min:0'],
      'discount' => ['sometimes', 'numeric', 'min:0'],
      'is_featured' => ['sometimes', 'boolean'],
    ];
  }
}