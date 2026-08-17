<?php

namespace App\Http\Requests\Admin\AdminUser;

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
            'account' => 'required|string|unique:admin_user,account,' . $this->user->getKey(),
            'password' => 'nullable|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'is_active' => 'boolean',
            'role_ids' => 'nullable|array',
            'role_ids.*' => 'exists:admin_role,id',
            'direct_permission_ids' => 'nullable|array',
            'direct_permission_ids.*' => 'exists:admin_permission,id',
        ];
    }
}
