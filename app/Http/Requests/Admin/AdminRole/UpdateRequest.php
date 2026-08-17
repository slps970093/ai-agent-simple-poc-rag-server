<?php

namespace App\Http\Requests\Admin\AdminRole;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'display_name' => 'required|array',
            'display_name.*' => 'string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:255',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:admin_permission,id',
        ];
    }
}
