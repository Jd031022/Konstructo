<?php

use App\Http\Controllers\Api\ChatController;
use Illuminate\Support\Facades\Route;

Route::post('/chat/send', [ChatController::class, 'send']);
Route::get('/chat/history', [ChatController::class, 'history']);