<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
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
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'price' => 'sometimes|numeric|min:0',
            'quantity' => 'sometimes|integer|min:0',
            'discountPercentage' => 'sometimes|numeric|min:0|max:100',
            'priceAfterDiscount' => 'sometimes|numeric|min:0',
            'availability' => 'sometimes|boolean',
            'item_image' => 'sometimes|image|mimes:png,jpg,jpeg|max:2048',
            'details_image' => 'sometimes|array',
            'details_image.*' => 'image|mimes:png,jpg,jpeg|max:2048',
            
        ];
    }
}
