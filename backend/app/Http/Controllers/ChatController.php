<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        try {
            $message = trim((string) $request->input('message', ''));

            if ($message === '') {
                return response()->json([
                    'error' => 'Message is required.',
                ], 422);
            }

            $response = Http::timeout(60)->post(
                'https://small-chat-ml-service.onrender.com/chat',
                [
                    'message' => $message,
                ]
            );

            $payload = $response->json();
            $answer = $payload['response'] ?? 'Sorry, I could not generate a response.';

            Chat::create([
                'message' => $message,
                'response' => $answer,
            ]);

            return response()->json([
                'response' => $answer,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
