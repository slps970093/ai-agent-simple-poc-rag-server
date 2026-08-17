<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BotChannel;
use App\Enums\BotStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Bot\StoreRequest;
use App\Http\Requests\Admin\Bot\UpdateRequest;
use App\Models\Bot;
use Inertia\Inertia;

class AiBotController extends Controller
{
    public function index()
    {
        $bots = Bot::orderBy('created_at', 'desc')
            ->get()
            ->map(fn($bot) => [
                'id' => $bot->getKey(),
                'name' => $bot->name,
                'channel' => $bot->channel->value,
                'channel_label' => $bot->channel->label(),
                'status' => $bot->status->value,
                'created_at' => $bot->created_at->format('Y-m-d H:i:s'),
            ]);

        return Inertia::render('Bot/Index', [
            'bots' => $bots,
        ]);
    }

    public function create()
    {
        return Inertia::render('Bot/Create', [
            'channels' => $this->channelOptions(),
        ]);
    }

    public function store(StoreRequest $request)
    {
        $validated = $request->validated();

        Bot::create([
            'name' => $validated['name'],
            'channel' => $validated['channel'],
            'identity' => $validated['identity'] ?? null,
            'allowed_actions' => $validated['allowed_actions'] ?? null,
            'restricted_actions' => $validated['restricted_actions'] ?? null,
            'forbidden_actions' => $validated['forbidden_actions'] ?? null,
            'status' => ($validated['is_active'] ?? true) ? BotStatus::Active : BotStatus::Inactive,
        ]);

        return redirect()->route('admin.ai-bots.index')
            ->with('success', '機器人已建立');
    }

    public function edit(Bot $aiBot)
    {
        return Inertia::render('Bot/Edit', [
            'bot' => [
                'id' => $aiBot->getKey(),
                'name' => $aiBot->name,
                'api_key' => $aiBot->api_key,
                'channel' => $aiBot->channel->value,
                'identity' => $aiBot->identity,
                'allowed_actions' => $aiBot->allowed_actions,
                'restricted_actions' => $aiBot->restricted_actions,
                'forbidden_actions' => $aiBot->forbidden_actions,
                'status' => $aiBot->status->value,
                'is_active' => $aiBot->status === BotStatus::Active,
            ],
            'channels' => $this->channelOptions(),
        ]);
    }

    public function update(UpdateRequest $request, Bot $aiBot)
    {
        $validated = $request->validated();

        $aiBot->update([
            'name' => $validated['name'],
            'channel' => $validated['channel'],
            'identity' => $validated['identity'] ?? null,
            'allowed_actions' => $validated['allowed_actions'] ?? null,
            'restricted_actions' => $validated['restricted_actions'] ?? null,
            'forbidden_actions' => $validated['forbidden_actions'] ?? null,
            'status' => ($validated['is_active'] ?? true) ? BotStatus::Active : BotStatus::Inactive,
        ]);

        if ($validated['regenerate_api_key'] ?? false) {
            $aiBot->update(['api_key' => Bot::generateApiKey()]);
        }

        return redirect()->route('admin.ai-bots.index')
            ->with('success', '機器人已更新');
    }

    public function destroy(Bot $aiBot)
    {
        $aiBot->delete();

        return back()->with('success', '機器人已刪除');
    }

    private function channelOptions(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->label(),
        ], BotChannel::cases());
    }
}
