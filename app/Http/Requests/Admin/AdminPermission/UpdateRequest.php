<?php

namespace App\Http\Requests\Admin\AdminPermission;

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
            'module' => 'required|string|max:255',
            'action' => 'required|string|max:255',
            'display_name' => 'required|array',
            'display_name.*' => 'string|max:255',
            'description' => 'nullable|array',
            'description.*' => 'nullable|string|max:255',
        ];
    }
}
