<?php

namespace Tests\Feature;

use App\Models\Chat;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ChatControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_chat_endpoint_returns_the_assistant_reply_and_persists_the_chat(): void
    {
        Http::fake([
            'https://small-chat-ml-service.onrender.com/chat' => Http::response([
                'response' => 'Hello from the bot',
            ], 200),
        ]);

        $response = $this->postJson('/api/chat', [
            'message' => 'hello',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'response' => 'Hello from the bot',
            ]);

        $this->assertDatabaseHas('chats', [
            'message' => 'hello',
            'response' => 'Hello from the bot',
        ]);
    }
}
