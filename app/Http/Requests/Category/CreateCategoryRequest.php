<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;

class CreateCategoryRequest extends FormRequest
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
      'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
      'description' => ['required', 'string', 'max:255'],
      'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
      'image_public_id' => ['nullable', 'string'],
    ];
  }
}