<?php

namespace App\Http\Controllers;

use App\Enums\BotStatus;
use App\Models\Bot;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AiBotInstructionsController extends Controller
{
    public function show(Request $request, Bot $aiBot): Response
    {
        $token = $request->query('token');

        if (
            ! is_string($token)
            || $aiBot->status !== BotStatus::Active
            || ! hash_equals($aiBot->identity_token, $token)
        ) {
            abort(404);
        }

        return response($this->instructions($aiBot), 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    private function instructions(Bot $bot): string
    {
        return implode(PHP_EOL, [
            '【Bot 身份設定】',
            $this->valueOrDefault($bot->identity),
            '',
            '【可以回答／執行的內容】',
            $this->formatRules($bot->allowed_actions),
            '',
            '【不可以回答的內容】',
            $this->formatRules($bot->restricted_actions),
            '',
            '【絕對禁止的行為】',
            $this->formatRules($bot->forbidden_actions),
            '',
        ]);
    }

    private function valueOrDefault(?string $value): string
    {
        return filled($value) ? trim($value) : '（未設定）';
    }

    private function formatRules(?string $rules): string
    {
        // 支援換行或逗號（全形、半形）分隔多條規則
        $items = array_values(array_filter(array_map(
            'trim',
            preg_split('/\r\n|\r|\n|,|，/', $rules ?? '')
        ), fn (string $item) => $item !== ''));

        if (empty($items)) {
            return '（未設定）';
        }

        // 只剩一條規則時不加項目符號，避免多餘的「- 」
        if (count($items) === 1) {
            return $items[0];
        }

        return collect($items)->map(fn (string $item) => '- '.$item)->implode(PHP_EOL);
    }
}
