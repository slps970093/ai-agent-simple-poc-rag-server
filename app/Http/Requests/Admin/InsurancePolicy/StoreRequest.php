<?php

namespace App\Http\Requests\Admin\InsurancePolicy;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
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
            'insurance_company_id' => ['required', 'integer', Rule::exists('insurance_companies', 'id')],
            'policy_code' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('insurance_policies', 'policy_code')->where('insurance_company_id', $this->input('insurance_company_id'))],
            'name' => ['required', 'string', 'max:255'],
            'version' => ['required', 'string', 'max:50'],
            'effective_date' => ['nullable', 'date'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'policy_code' => Str::upper(Str::squish((string) $this->input('policy_code'))),
            'name' => Str::squish((string) $this->input('name')),
            'version' => Str::squish((string) $this->input('version', '1.0')),
        ]);
    }
}
