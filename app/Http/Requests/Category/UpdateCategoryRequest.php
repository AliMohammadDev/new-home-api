<?php

namespace App\Http\Requests\Category;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
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
      'name' => ['sometimes', 'array'],
      'name.en' => ['sometimes', 'string', 'max:255'],
      'name.ar' => ['sometimes', 'string', 'max:255'],

      'description' => ['sometimes', 'array'],
      'description.en' => ['sometimes', 'string'],
      'description.ar' => ['sometimes', 'string'],

    ];
  }


}
