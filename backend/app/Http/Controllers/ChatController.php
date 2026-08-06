<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        try {
            $message = $request->input('message');

            $response = Http::timeout(60)->post(
                'https://small-chat-ml-service.onrender.com/chat',
                ['message' => $message]
            );

            if (! $response->successful()) {
                Log::error('ML service request failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'error' => 'ML service returned an error',
                ], 502);
            }

            $data = $response->json();

            if (! is_array($data) || ! array_key_exists('response', $data)) {
                Log::error('Invalid ML service response', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'error' => 'Invalid response from ML service',
                ], 502);
            }

            $answer = $data['response'];

            Chat::create([
                'message' => $message,
                'response' => $answer,
            ]);

            return response()->json(['response' => $answer]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}