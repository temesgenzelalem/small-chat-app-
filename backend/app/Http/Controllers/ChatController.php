use Illuminate\Support\Facades\Log;

public function chat(Request $request)
{
    try {
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
    } catch (\Exception $e) {
        Log::error($e->getMessage());
        return response()->json([
            'error' => $e->getMessage()
        ], 500);
    }
}