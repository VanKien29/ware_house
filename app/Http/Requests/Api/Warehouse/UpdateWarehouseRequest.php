<?php

namespace App\Http\Requests\Api\Warehouse;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
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
            'code' => 'required|string|unique:warehouses,code,'.$this->route('warehouse'),
            'name' => 'required|string',
            'address' => 'required|string',
            'manager_id' => 'nullable|exists:users,id',
            'status' => 'required|in:active,inactive',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Mã kho không được để trống',
            'code.string' => 'Mã kho phải là chuỗi',
            'code.unique' => 'Mã kho đã tồn tại',
            'name.required' => 'Tên kho không được để trống',
            'name.string' => 'Tên kho phải là chuỗi',
            'address.required' => 'Địa chỉ kho không được để trống',
            'address.string' => 'Địa chỉ kho phải là chuỗi',
            'manager_id.exists' => 'Quản lý kho không tồn tại',
            'status.required' => 'Trạng thái kho không được để trống',
            'status.in' => 'Trạng thái kho phải là active hoặc inactive',
        ];
    }
}
