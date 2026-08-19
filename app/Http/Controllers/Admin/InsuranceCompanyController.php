<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InsuranceCompany\StoreRequest;
use App\Http\Requests\Admin\InsuranceCompany\UpdateRequest;
use App\Models\InsuranceCompany;
use Inertia\Inertia;

class InsuranceCompanyController extends Controller
{
    public function index()
    {
        return Inertia::render('InsuranceCompany/Index', [
            'companies' => InsuranceCompany::query()
                ->withCount('policies')
                ->orderBy('name')
                ->get()
                ->map(fn (InsuranceCompany $company) => $this->companyData($company)),
        ]);
    }

    public function create()
    {
        return Inertia::render('InsuranceCompany/Create');
    }

    public function store(StoreRequest $request)
    {
        InsuranceCompany::create($request->validated());

        return redirect()->route('admin.insurance-companies.index')->with('success', '保險公司已建立');
    }

    public function edit(InsuranceCompany $company)
    {
        return Inertia::render('InsuranceCompany/Edit', [
            'company' => $this->companyData($company),
        ]);
    }

    public function update(UpdateRequest $request, InsuranceCompany $company)
    {
        $company->update($request->validated());

        return redirect()->route('admin.insurance-companies.index')->with('success', '保險公司已更新');
    }

    public function destroy(InsuranceCompany $company)
    {
        if ($company->policies()->exists()) {
            return back()->with('error', '此保險公司仍有保單，無法刪除');
        }

        $company->delete();

        return back()->with('success', '保險公司已刪除');
    }

    private function companyData(InsuranceCompany $company): array
    {
        return [
            'id' => $company->getKey(),
            'code' => $company->code,
            'name' => $company->name,
            'is_active' => $company->is_active,
            'policies_count' => $company->policies_count,
            'created_at' => $company->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
