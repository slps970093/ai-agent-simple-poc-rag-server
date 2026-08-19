<?php

namespace App\Services;

use App\Models\InsuranceCompany;
use App\Models\InsurancePolicy;
use App\Models\InsurancePolicyContent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Pgvector\Laravel\Distance;
use RuntimeException;

class InsurancePolicySearchService
{
    public function __construct(
        private readonly PolicyEmbeddingService $embeddingService,
    ) {}

    public function findActiveCompany(string $company): ?InsuranceCompany
    {
        $company = Str::lower($company);

        return InsuranceCompany::query()
            ->where('is_active', true)
            ->where(static function ($query) use ($company): void {
                $query->whereRaw('LOWER(code) = ?', [$company])
                    ->orWhereRaw('LOWER(name) = ?', [$company]);
            })
            ->first();
    }

    public function findActivePolicy(InsuranceCompany $company, string $policyCode): ?InsurancePolicy
    {
        return $company->policies()
            ->where('is_active', true)
            ->whereRaw('LOWER(policy_code) = ?', [Str::lower($policyCode)])
            ->first();
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function search(InsurancePolicy $policy, string $question, ?int $requestedLimit = null): array
    {
        if (DB::getDriverName() !== 'pgsql') {
            throw new RuntimeException('The policy search service requires PostgreSQL with pgvector.');
        }

        $queryEmbedding = $this->embeddingService->embedWithMetadata($question);
        $limit = min(20, max(1, $requestedLimit ?? (int) config('insurance.search.limit')));
        $minimumSimilarity = (float) config('insurance.search.minimum_similarity');

        return InsurancePolicyContent::query()
            ->where('insurance_policy_id', $policy->getKey())
            ->where('embedding_model', $queryEmbedding['model'])
            ->nearestNeighbors('embedding', $queryEmbedding['embedding'], Distance::Cosine)
            ->limit($limit)
            ->get()
            ->filter(static fn (InsurancePolicyContent $content): bool => 1 - $content->neighbor_distance >= $minimumSimilarity)
            ->map(static function (InsurancePolicyContent $content): array {
                return [
                    'id' => $content->getKey(),
                    'content' => $content->content,
                    'embedding_model' => $content->embedding_model,
                    'page_from' => $content->page_from,
                    'page_to' => $content->page_to,
                    'sort_order' => $content->sort_order,
                    'source_metadata' => $content->source_metadata,
                    'similarity' => round(1 - $content->neighbor_distance, 4),
                ];
            })
            ->values()
            ->all();
    }
}
