<?php

namespace App\Http\Requests\Admin\InsuranceCompany;

use App\Models\InsuranceCompany;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        /** @var InsuranceCompany $company */
        $company = $this->route('company');

        return [
            'code' => ['required', 'string', 'max:20', 'regex:/^[A-Z0-9_-]+$/', Rule::unique('insurance_companies', 'code')->ignore($company)],
            'name' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'code' => Str::upper(Str::squish((string) $this->input('code'))),
            'name' => Str::squish((string) $this->input('name')),
        ]);
    }
}
