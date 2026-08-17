<?php

namespace App\Http\Requests\Admin\AdminMenu;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|array',
            'name.*' => 'string|max:255',
            'parent_id' => 'nullable|exists:admin_menu,id',
            'icon' => 'nullable|string|max:255',
            'route_name' => 'nullable|string|max:255',
            'route_params' => 'nullable|array',
            'sort_order' => 'integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
