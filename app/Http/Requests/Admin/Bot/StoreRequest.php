<?php

namespace App\Http\Requests\Admin\Bot;

use App\Enums\BotChannel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'channel' => ['required', Rule::enum(BotChannel::class)],
            'identity' => 'nullable|string',
            'allowed_actions' => 'nullable|string',
            'restricted_actions' => 'nullable|string',
            'forbidden_actions' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
}
