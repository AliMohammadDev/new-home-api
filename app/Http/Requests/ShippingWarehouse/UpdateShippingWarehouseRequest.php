<?php

namespace App\Http\Requests\ShippingWarehouse;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingWarehouseRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return false;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'product_variant_id' => 'sometimes|exists:product_variants,id',
      'warehouse_id' => 'sometimes|exists:warehouses,id',
      'arrival_time' => 'sometimes|date',
      'amount' => 'sometimes|numeric|min:0',
      'unit_name' => 'sometimes|string',
      'unit_capacity' => 'sometimes|numeric',
    ];
  }
}