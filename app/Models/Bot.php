<?php

namespace App\Models;

use App\Enums\BotChannel;
use App\Enums\BotStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Bot extends Model
{
    use SoftDeletes;

    protected $table = 'ai_bots';

    protected $fillable = [
        'name',
        'api_key',
        'identity_token',
        'channel',
        'identity',
        'allowed_actions',
        'restricted_actions',
        'forbidden_actions',
        'status',
    ];

    protected $casts = [
        'channel' => BotChannel::class,
        'status' => BotStatus::class,
    ];

    protected static function booted(): void
    {
        static::creating(function (Bot $bot) {
            if (empty($bot->api_key)) {
                $bot->api_key = self::generateApiKey();
            }

            if (empty($bot->identity_token)) {
                $bot->identity_token = self::generateIdentityToken();
            }
        });
    }

    public static function generateApiKey(): string
    {
        do {
            $key = 'bot_'.Str::random(40);
        } while (self::withTrashed()->where('api_key', $key)->exists());

        return $key;
    }

    public static function generateIdentityToken(): string
    {
        do {
            $token = 'identity_'.Str::random(40);
        } while (self::withTrashed()->where('identity_token', $token)->exists());

        return $token;
    }
}
