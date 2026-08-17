<?php

use App\Http\Controllers\Api\BotConfigController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth.bot')->group(function () {
    Route::get('/bot/config', [BotConfigController::class, 'show']);
});
