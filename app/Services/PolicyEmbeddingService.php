<?php

namespace App\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class PolicyEmbeddingService
{
    /**
     * @return array<int, float>
     */
    public function embed(string $input): array
    {
        return $this->embedWithMetadata($input)['embedding'];
    }

    /**
     * @return array{embedding: array<int, float>, model: string}
     */
    public function embedWithMetadata(string $input): array
    {
        $url = config('insurance.embedding.url');
        $apiKey = config('insurance.embedding.api_key');
        $dimensions = (int) config('insurance.embedding.dimensions');

        if (blank($url)) {
            throw new RuntimeException('The policy embedding provider is not configured.');
        }

        try {
            $request = Http::acceptJson()
                ->asJson()
                ->timeout((int) config('insurance.embedding.timeout'));

            if (filled($apiKey)) {
                $request = $request->withToken($apiKey);
            }

            $response = $request->post($url, [
                'texts' => [$input],
            ])->throw();
        } catch (ConnectionException|RequestException $exception) {
            throw new RuntimeException('The policy embedding provider is unavailable.', previous: $exception);
        }

        $payload = $response->json();
        $embedding = data_get($payload, 'items.0.embedding');
        $model = data_get($payload, 'model');
        $returnedDimensions = data_get($payload, 'dimensions');

        if (
            ! is_string($model)
            || blank($model)
            || ! is_numeric($returnedDimensions)
            || (int) $returnedDimensions !== $dimensions
            || ! is_array($embedding)
            || count($embedding) !== $dimensions
        ) {
            throw new RuntimeException('The policy embedding provider returned an invalid vector.');
        }

        foreach ($embedding as $value) {
            if (! is_numeric($value) || ! is_finite((float) $value)) {
                throw new RuntimeException('The policy embedding provider returned an invalid vector.');
            }
        }

        return [
            'embedding' => array_map(static fn ($value): float => (float) $value, $embedding),
            'model' => $model,
        ];
    }
}
