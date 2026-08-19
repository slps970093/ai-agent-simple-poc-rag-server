<?php

return [
    'embedding' => [
        'url' => env('POLICY_EMBEDDING_URL'),
        'api_key' => env('POLICY_EMBEDDING_API_KEY'),
        'dimensions' => (int) env('POLICY_EMBEDDING_DIMENSIONS', 512),
        'timeout' => (int) env('POLICY_EMBEDDING_TIMEOUT', 15),
    ],

    'search' => [
        'limit' => (int) env('POLICY_SEARCH_LIMIT', 5),
        'minimum_similarity' => (float) env('POLICY_SEARCH_MINIMUM_SIMILARITY', 0.5),
    ],
];
