<?php

namespace App\Http\Requests\Admin\InsurancePolicyContent;

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
            'insurance_policy_id' => ['required', 'integer', Rule::exists('insurance_policies', 'id')],
            'content' => ['required', 'string', 'max:100000'],
            'page_from' => ['nullable', 'integer', 'min:1'],
            'page_to' => ['nullable', 'integer', 'min:1', 'gte:page_from'],
            'sort_order' => ['required', 'integer', 'min:0'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('content')) {
            $this->merge(['content' => trim((string) $this->input('content'))]);
        }
    }
}
