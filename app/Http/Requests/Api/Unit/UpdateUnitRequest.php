<?php

namespace App\Http\Requests\Api\Unit;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUnitRequest extends FormRequest
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
            'code' => 'required|string|max:50|unique:units,code,'.$this->route('unit'),
            'name' => 'required|string|max:255|unique:units,name,'.$this->route('unit'),
            'precision' => 'required|integer|min:0|max:6',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Mã đơn vị không được để trống',
            'code.string' => 'Mã đơn vị phải là chuỗi',
            'code.max' => 'Mã đơn vị không được vượt quá 50 ký tự',
            'code.unique' => 'Mã đơn vị đã tồn tại',

            'name.required' => 'Tên đơn vị không được để trống',
            'name.string' => 'Tên đơn vị phải là chuỗi',
            'name.max' => 'Tên đơn vị không được vượt quá 255 ký tự',
            'name.unique' => 'Tên đơn vị đã tồn tại',

            'precision.required' => 'Độ chính xác không được để trống',
            'precision.integer' => 'Độ chính xác phải là số nguyên',
            'precision.min' => 'Độ chính xác không được nhỏ hơn 0',
            'precision.max' => 'Độ chính xác không được lớn hơn 6',
        ];
    }
}
