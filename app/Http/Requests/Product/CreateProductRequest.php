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
      'name' => ['required', 'array'],
      'name.en' => ['required', 'string', 'max:255'],
      'name.ar' => ['required', 'string', 'max:255'],

      'body' => ['required', 'array'],
      'body.en' => ['required', 'string', 'max:1000'],
      'body.ar' => ['required', 'string', 'max:1000'],

      'category_id' => ['required', 'exists:categories,id'],
      'is_featured' => ['nullable', 'boolean'],

    ];
  }
}