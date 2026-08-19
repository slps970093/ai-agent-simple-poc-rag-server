<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InsurancePolicy\StoreRequest;
use App\Http\Requests\Admin\InsurancePolicy\UpdateRequest;
use App\Models\InsuranceCompany;
use App\Models\InsurancePolicy;
use Inertia\Inertia;

class InsurancePolicyController extends Controller
{
    public function index()
    {
        return Inertia::render('InsurancePolicy/Index', [
            'policies' => InsurancePolicy::query()
                ->with('company')
                ->withCount('contents')
                ->orderBy('policy_code')
                ->get()
                ->map(fn (InsurancePolicy $policy) => $this->policyData($policy)),
        ]);
    }

    public function create()
    {
        return Inertia::render('InsurancePolicy/Create', [
            'companies' => $this->companyOptions(),
        ]);
    }

    public function store(StoreRequest $request)
    {
        InsurancePolicy::create($request->validated());

        return redirect()->route('admin.insurance-policies.index')->with('success', '保單已建立');
    }

    public function edit(InsurancePolicy $policy)
    {
        return Inertia::render('InsurancePolicy/Edit', [
            'policy' => $this->policyData($policy->load('company')),
            'companies' => $this->companyOptions(),
        ]);
    }

    public function update(UpdateRequest $request, InsurancePolicy $policy)
    {
        $policy->update($request->validated());

        return redirect()->route('admin.insurance-policies.index')->with('success', '保單已更新');
    }

    public function destroy(InsurancePolicy $policy)
    {
        if ($policy->contents()->exists()) {
            return back()->with('error', '此保單仍有條款內文，無法刪除');
        }

        $policy->delete();

        return back()->with('success', '保單已刪除');
    }

    private function companyOptions(): array
    {
        return InsuranceCompany::query()->orderBy('name')->get()->map(fn (InsuranceCompany $company) => [
            'value' => $company->getKey(),
            'label' => sprintf('%s - %s%s', $company->code, $company->name, $company->is_active ? '' : '（已停用）'),
            'is_active' => $company->is_active,
        ])->all();
    }

    private function policyData(InsurancePolicy $policy): array
    {
        return [
            'id' => $policy->getKey(),
            'insurance_company_id' => $policy->insurance_company_id,
            'company_name' => $policy->company?->name,
            'company_code' => $policy->company?->code,
            'policy_code' => $policy->policy_code,
            'name' => $policy->name,
            'version' => $policy->version,
            'effective_date' => $policy->effective_date?->toDateString(),
            'is_active' => $policy->is_active,
            'contents_count' => $policy->contents_count,
            'created_at' => $policy->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
