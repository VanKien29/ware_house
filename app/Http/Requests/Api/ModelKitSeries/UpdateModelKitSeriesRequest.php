<?php

namespace App\Http\Requests\Api\ModelKitSeries;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateModelKitSeriesRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:model_kit_series,name,'.$this->route('model_kit_series'),
            'manufacturer_id' => 'required|exists:model_kit_manufacturers,id',
            'universe' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Tên series không được để trống',
            'name.string' => 'Tên series phải là một chuỗi',
            'name.max' => 'Tên series không được vượt quá 255 ký tự',
            'name.unique' => 'Tên series đã tồn tại',
            'manufacturer_id.required' => 'Nhà sản xuất không được để trống',
            'manufacturer_id.exists' => 'Nhà sản xuất không tồn tại',
            'universe.string' => 'Universe phải là một chuỗi',
            'universe.max' => 'Universe không được vượt quá 255 ký tự',
            'description.string' => 'Mô tả phải là một chuỗi',
        ];
    }
}
