<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateItemRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'priceAfterDiscount' => 'nullable|numeric|min:0',
            'DiscountPercentage' => 'nullable|numeric|min:0|max:100',
            'item_image' => 'required|image|mimes:png,jpg,jpeg|max:2048',
            'details_image' => 'required|array',
            'details_image.*' => 'image|mimes:png,jpg,jpeg|max:2048',
        ];
    }
}
