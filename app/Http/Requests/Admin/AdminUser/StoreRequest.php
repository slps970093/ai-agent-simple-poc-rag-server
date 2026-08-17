<?php

namespace App\Http\Requests\Admin\AdminUser;

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
            'account' => 'required|string|unique:admin_user,account',
            'password' => 'required|string|min:8|confirmed',
            'name' => 'required|string|max:255',
            'is_root' => 'boolean',
            'is_active' => 'boolean',
        ];
    }
}
