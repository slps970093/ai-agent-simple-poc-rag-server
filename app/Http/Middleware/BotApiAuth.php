<?php

namespace App\Http\Middleware;

use App\Enums\BotStatus;
use App\Models\Bot;
use Closure;
use Illuminate\Http\Request;

class BotApiAuth
{
    public function handle(Request $request, Closure $next)
    {
        $apiKey = $request->header('X-Bot-Api-Key');

        if (! $apiKey) {
            return response()->json(['error' => 'Missing API key'], 401);
        }

        $bot = Bot::where('api_key', $apiKey)
            ->where('status', BotStatus::Active)
            ->first();

        if (! $bot) {
            return response()->json(['error' => 'Invalid or inactive bot'], 401);
        }

        $request->attributes->set('bot', $bot);

        return $next($request);
    }
}
