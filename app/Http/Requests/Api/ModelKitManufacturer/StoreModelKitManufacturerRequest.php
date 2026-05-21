<?php

namespace App\Http\Requests\Api\ModelKitManufacturer;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreModelKitManufacturerRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:model_kit_manufacturers',
            'slug' => 'required|string|max:255|unique:model_kit_manufacturers',
            'country' => 'nullable|string|max:255',
            'website_url' => 'nullable|url',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên nhà sản xuất không được để trống',
            'name.string' => 'Tên nhà sản xuất phải là một chuỗi',
            'name.max' => 'Tên nhà sản xuất không được vượt quá 255 ký tự',
            'name.unique' => 'Tên nhà sản xuất đã tồn tại',

            'slug.required' => 'Slug không được để trống',
            'slug.string' => 'Slug phải là một chuỗi',
            'slug.max' => 'Slug không được vượt quá 255 ký tự',
            'slug.unique' => 'Slug đã tồn tại',

            'country.string' => 'Quốc gia phải là một chuỗi',
            'country.max' => 'Quốc gia không được vượt quá 255 ký tự',

            'website_url.url' => 'URL trang web không hợp lệ',
            'description.string' => 'Mô tả phải là một chuỗi',
        ];
    }
}
