<?php

namespace App\Http\Requests\Api\Product;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'category_id' => 'nullable|exists:product_categories,id',
            'base_unit_id' => 'required|exists:units,id',
            'manufacturer_id' => 'nullable|exists:model_kit_manufacturers,id',
            'series_id' => 'nullable|exists:model_kit_series,id',
            'kit_code' => 'nullable|string|max:100|unique:products,kit_code',
            'name' => 'required|string|max:255|unique:products,name',
            'grade' => 'nullable|string|max:50',
            'scale' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
            'runner_count' => 'nullable|integer|min:0|max:999',
            'release_date' => 'nullable|date',
            'box_art_url' => 'nullable|url|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:active,inactive,discontinued,pre_order',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên sản phẩm không được để trống',
            'name.string' => 'Tên sản phẩm phải là một chuỗi',
            'name.max' => 'Tên sản phẩm không được vượt quá 255 ký tự',

            'category_id.exists' => 'Danh mục sản phẩm không tồn tại',

            'base_unit_id.required' => 'Đơn vị tính không được để trống',
            'base_unit_id.exists' => 'Đơn vị tính không tồn tại',

            'manufacturer_id.exists' => 'Hãng sản xuất không tồn tại',

            'series_id.exists' => 'Dòng mô hình không tồn tại',

            'kit_code.string' => 'Mã kit phải là một chuỗi',
            'kit_code.max' => 'Mã kit không được vượt quá 100 ký tự',
            'kit_code.unique' => 'Mã kit đã tồn tại',

            'grade.string' => 'Grade phải là một chuỗi',
            'grade.max' => 'Grade không được vượt quá 50 ký tự',

            'scale.string' => 'Tỉ lệ phải là một chuỗi',
            'scale.max' => 'Tỉ lệ không được vượt quá 50 ký tự',

            'material.string' => 'Chất liệu phải là một chuỗi',
            'material.max' => 'Chất liệu không được vượt quá 100 ký tự',

            'runner_count.integer' => 'Số runner phải là số nguyên',
            'runner_count.min' => 'Số runner không được âm',
            'runner_count.max' => 'Số runner không được vượt quá 999',

            'release_date.date' => 'Ngày phát hành không hợp lệ',

            'box_art_url.url' => 'Link box art không hợp lệ',
            'box_art_url.max' => 'Link box art không được vượt quá 255 ký tự',

            'description.string' => 'Mô tả phải là một chuỗi',

            'status.required' => 'Trạng thái không được để trống',
            'status.in' => 'Trạng thái không hợp lệ',
        ];
    }
}
