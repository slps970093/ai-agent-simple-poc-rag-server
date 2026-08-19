<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InsurancePolicyContent\StoreRequest;
use App\Http\Requests\Admin\InsurancePolicyContent\UpdateRequest;
use App\Models\InsurancePolicy;
use App\Models\InsurancePolicyContent;
use App\Services\PolicyEmbeddingService;
use Inertia\Inertia;
use RuntimeException;

class InsurancePolicyContentController extends Controller
{
    public function __construct(
        private readonly PolicyEmbeddingService $embeddingService,
    ) {}

    public function index()
    {
        return Inertia::render('InsurancePolicyContent/Index', [
            'contents' => InsurancePolicyContent::query()
                ->with('policy.company')
                ->orderBy('insurance_policy_id')
                ->orderBy('sort_order')
                ->get()
                ->map(fn (InsurancePolicyContent $content) => $this->contentData($content, true)),
        ]);
    }

    public function create()
    {
        return Inertia::render('InsurancePolicyContent/Create', [
            'policies' => $this->policyOptions(),
        ]);
    }

    public function store(StoreRequest $request)
    {
        $attributes = $request->validated();

        try {
            $embedding = $this->embeddingService->embedWithMetadata($attributes['content']);
        } catch (RuntimeException $exception) {
            report($exception);

            return back()->withInput()->with('error', '保單內文向量產生失敗，請確認 RAG embedding 服務後再試一次。');
        }

        $content = new InsurancePolicyContent;
        $content->fill($attributes);
        $content->forceFill([
            'embedding' => $embedding['embedding'],
            'embedding_model' => $embedding['model'],
        ])->save();

        return redirect()->route('admin.insurance-policy-contents.index')->with('success', '保單內文與向量已建立');
    }

    public function edit(InsurancePolicyContent $content)
    {
        return Inertia::render('InsurancePolicyContent/Edit', [
            'contentItem' => $this->contentData($content->load('policy.company')),
            'policies' => $this->policyOptions(),
        ]);
    }

    public function update(UpdateRequest $request, InsurancePolicyContent $content)
    {
        $attributes = $request->validated();

        try {
            $embedding = $this->embeddingService->embedWithMetadata($attributes['content']);
        } catch (RuntimeException $exception) {
            report($exception);

            return back()->withInput()->with('error', '保單內文向量產生失敗，請確認 RAG embedding 服務後再試一次。');
        }

        $content->fill($attributes);
        $content->forceFill([
            'embedding' => $embedding['embedding'],
            'embedding_model' => $embedding['model'],
        ])->save();

        return redirect()->route('admin.insurance-policy-contents.index')->with('success', '保單內文與向量已更新');
    }

    public function destroy(InsurancePolicyContent $content)
    {
        $content->delete();

        return back()->with('success', '保單內文已刪除');
    }

    private function policyOptions(): array
    {
        return InsurancePolicy::query()->with('company')->orderBy('policy_code')->get()->map(fn (InsurancePolicy $policy) => [
            'value' => $policy->getKey(),
            'label' => sprintf('%s / %s - %s', $policy->company->name, $policy->policy_code, $policy->name),
        ])->all();
    }

    private function contentData(InsurancePolicyContent $content, bool $preview = false): array
    {
        return [
            'id' => $content->getKey(),
            'insurance_policy_id' => $content->insurance_policy_id,
            'policy_label' => $content->relationLoaded('policy')
                ? sprintf('%s / %s - %s', $content->policy->company->name, $content->policy->policy_code, $content->policy->name)
                : null,
            'content' => $preview ? str($content->content)->limit(120)->toString() : $content->content,
            'page_from' => $content->page_from,
            'page_to' => $content->page_to,
            'sort_order' => $content->sort_order,
            'embedding_model' => $content->embedding_model,
            'created_at' => $content->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
