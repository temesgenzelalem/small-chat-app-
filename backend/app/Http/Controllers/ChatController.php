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

        $response = Http::timeout(60)->post(
            'https://small-chat-ml-service.onrender.com/chat',
            [
                'message' => $message
            ]
        );

        $answer = $response->json()['response'];

        Chat::create([
            'message' => $message,
            'response' => $answer
        ]);

        return [
            'response' => $answer
        ];
    }
}