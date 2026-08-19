<?php

namespace App\Mcp\Tools;

use App\Services\InsurancePolicySearchService;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Illuminate\JsonSchema\Types\Type;
use Laravel\Mcp\Request;
use Laravel\Mcp\Response;
use Laravel\Mcp\Server\Attributes\Description;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Tool;
use RuntimeException;

#[Name('search_insurance_policy')]
#[Description('Searches active insurance policy terms using semantic vector search. Use it to determine policy coverage conditions, exclusions, waiting periods, and claim requirements.')]
class SearchInsurancePolicyTool extends Tool
{
    public function handle(Request $request, InsurancePolicySearchService $policySearch): Response
    {
        $validated = $request->validate([
            'insurance_company' => ['required', 'string', 'max:255'],
            'policy_code' => ['required', 'string', 'max:20'],
            'question' => ['required', 'string', 'max:2000'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $company = $policySearch->findActiveCompany($validated['insurance_company']);

        if (! $company) {
            return Response::error('The requested active insurance company was not found.');
        }

        $policy = $policySearch->findActivePolicy($company, $validated['policy_code']);

        if (! $policy) {
            return Response::error('The requested active insurance policy was not found.');
        }

        try {
            $results = $policySearch->search(
                $policy,
                $validated['question'],
                $validated['limit'] ?? null,
            );
        } catch (RuntimeException $exception) {
            report($exception);

            return Response::error('The policy search service is temporarily unavailable.');
        }

        return Response::json([
            'insurance_company' => [
                'code' => $company->code,
                'name' => $company->name,
            ],
            'policy' => [
                'code' => $policy->policy_code,
                'name' => $policy->name,
                'version' => $policy->version,
                'effective_date' => $policy->effective_date?->toDateString(),
            ],
            'results' => $results,
        ]);
    }

    /**
     * @return array<string, Type>
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'insurance_company' => $schema->string()
                ->description('Insurance company code or exact name, for example FUBON.')
                ->required(),
            'policy_code' => $schema->string()
                ->description('Insurance policy code, for example XLT.')
                ->required(),
            'question' => $schema->string()
                ->description('The insurance coverage or claim question to search for.')
                ->required(),
            'limit' => $schema->integer()
                ->description('Optional maximum number of matching policy text segments to return, from 1 to 20.'),
        ];
    }
}
