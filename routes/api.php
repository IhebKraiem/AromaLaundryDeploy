<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WebhookController;
use App\Events\OrderCreated;

// Webhook route
Route::post('/webhook/wordpress-form', [WebhookController::class, 'handleWordPressForm']);
