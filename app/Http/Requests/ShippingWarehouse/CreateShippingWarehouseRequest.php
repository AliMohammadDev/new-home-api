<?php

namespace App\Http\Requests\ShippingWarehouse;

use Illuminate\Foundation\Http\FormRequest;

class CreateShippingWarehouseRequest extends FormRequest
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
      'product_variant_id' => 'required|exists:product_variants,id',
      'warehouse_id' => 'required|exists:warehouses,id',
      'arrival_time' => 'required|date',
      'amount' => 'required|numeric|min:0',
      'unit_name' => 'required|string',
      'unit_capacity' => 'required|numeric',
    ];
  }
}
