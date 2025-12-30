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
      'name' => ['required', 'array'],
      'name.en' => ['required', 'string', 'max:255'],
      'name.ar' => ['required', 'string', 'max:255'],

      'description' => ['required', 'array'],
      'description.en' => ['required', 'string'],
      'description.ar' => ['required', 'string'],

      'image' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:3072'],
    ];
  }

}