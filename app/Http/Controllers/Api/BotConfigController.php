<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BotConfigController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $bot = $request->attributes->get('bot');

        return response()->json([
            'id' => $bot->getKey(),
            'name' => $bot->name,
            'channel' => $bot->channel->value,
            'identity' => $bot->identity,
            'rules' => [
                'allowed' => $this->parseLines($bot->allowed_actions),
                'restricted' => $this->parseLines($bot->restricted_actions),
                'forbidden' => $this->parseLines($bot->forbidden_actions),
            ],
        ]);
    }

    private function parseLines(?string $text): array
    {
        if (empty($text)) {
            return [];
        }

        return array_values(
            array_filter(
                array_map('trim', explode("\n", $text))
            )
        );
    }
}
