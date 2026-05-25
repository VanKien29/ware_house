<?php

namespace App\Http\Requests\Api\ProductVariant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'sku' => 'required|string|max:100|unique:product_variants,sku',
            'barcode' => 'nullable|string|max:100|unique:product_variants,barcode',
            'variant_name' => 'nullable|string|max:255',
            'edition' => 'nullable|string|max:100',
            'box_condition' => 'nullable|string|max:100',
            'item_condition' => 'nullable|string|max:100',
            'has_manual' => 'required|boolean',
            'has_decals' => 'required|boolean',
            'purchase_price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'status' => 'required|in:active,inactive,discontinued,pre_order',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Sản phẩm không được để trống',
            'product_id.exists' => 'Sản phẩm không tồn tại',

            'sku.required' => 'SKU không được để trống',
            'sku.string' => 'SKU phải là một chuỗi',
            'sku.max' => 'SKU không được vượt quá 100 ký tự',
            'sku.unique' => 'SKU đã tồn tại',

            'barcode.string' => 'Barcode phải là một chuỗi',
            'barcode.max' => 'Barcode không được vượt quá 100 ký tự',
            'barcode.unique' => 'Barcode đã tồn tại',

            'variant_name.string' => 'Tên biến thể phải là một chuỗi',
            'variant_name.max' => 'Tên biến thể không được vượt quá 255 ký tự',

            'edition.string' => 'Phiên bản phải là một chuỗi',
            'edition.max' => 'Phiên bản không được vượt quá 100 ký tự',

            'box_condition.string' => 'Tình trạng hộp phải là một chuỗi',
            'box_condition.max' => 'Tình trạng hộp không được vượt quá 100 ký tự',

            'item_condition.string' => 'Tình trạng sản phẩm phải là một chuỗi',
            'item_condition.max' => 'Tình trạng sản phẩm không được vượt quá 100 ký tự',

            'has_manual.required' => 'Thông tin có sách hướng dẫn không được để trống',
            'has_manual.boolean' => 'Thông tin có sách hướng dẫn phải là true hoặc false',

            'has_decals.required' => 'Thông tin có decal hay không không được để trống',
            'has_decals.boolean' => 'Thông tin có decal hay không phải là true hoặc false',

            'purchase_price.numeric' => 'Giá mua phải là một số',
            'purchase_price.min' => 'Giá mua phải lớn hơn hoặc bằng 0',

            'sale_price.numeric' => 'Giá bán phải là một số',
            'sale_price.min' => 'Giá bán phải lớn hơn hoặc bằng 0',

            'min_stock.numeric' => 'Số lượng tồn tối thiểu phải là một số',
            'min_stock.min' => 'Số lượng tồn tối thiểu phải lớn hơn hoặc bằng 0',

            'status.required' => 'Trạng thái không được để trống',
            'status.in' => 'Trạng thái không hợp lệ',
        ];
    }
}
