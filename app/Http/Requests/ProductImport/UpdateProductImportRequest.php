<?php

namespace App\Http\Requests\ProductImport;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductImportRequest extends FormRequest
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
      'supplier_name' => ['sometimes', 'string', 'max:255'],
      'supplier_phone' => ['sometimes', 'string', 'max:20'],
      'import_date' => ['sometimes', 'date'],
      'address' => ['sometimes', 'string', 'max:500'],
      'notes' => ['sometimes', 'string'],
    ];
  }
}