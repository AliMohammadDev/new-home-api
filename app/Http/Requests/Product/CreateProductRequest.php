<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class CreateProductRequest extends FormRequest
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
      'name' => ['required', 'string', 'max:255'],
      'body' => ['required', 'string', 'max:255'],
      'category_id' => ['required', 'exists:categories,id'],
      'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
      'image_public_id' => ['nullable', 'string'],
      'price' => ['required', 'numeric', 'min:0'],
      'discount' => ['nullable', 'numeric', 'min:0'],
      'is_featured' => ['nullable', 'boolean'],

    ];
  }
}
