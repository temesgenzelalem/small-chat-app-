<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Chat;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');

        // Send message to Python ML chatbot
        $response = Http::post('https://small-chat-ml-service.onrender.com/chat', [
            'message' => $message
        ]);

        $answer = $response->json()['response'];

        // Save conversation to Neon PostgreSQL
        Chat::create([
            'message' => $message,
            'response' => $answer
        ]);

        return [
            'response' => $answer
        ];
    }
}