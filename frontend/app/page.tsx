"use client";

import { useState } from "react";
import axios from "axios";

export default function Home() {
  const [message, setMessage] = useState("");
  const [chat, setChat] = useState<
    { sender: string; text: string }[]
  >([]);

  const sendMessage = async () => {
    if (!message.trim()) return;

    const userMessage = message;

    setChat((prev) => [
      ...prev,
      { sender: "You", text: userMessage },
    ]);

    setMessage("");

    try {
      const res = await axios.post(
        "http://127.0.0.1:8000/api/chat",
        {
          message: userMessage,
        }
      );

      setChat((prev) => [
        ...prev,
        {
          sender: "Bot",
          text: res.data.response,
        },
      ]);
    } catch (error) {
      setChat((prev) => [
        ...prev,
        {
          sender: "Bot",
          text: "Server error",
        },
      ]);
    }
  };

  return (
    <div className="min-h-screen p-8">
      <h1 className="text-3xl font-bold mb-6">
        ML Chatbot
      </h1>

      <div className="border rounded p-4 h-[500px] overflow-y-auto">
        {chat.map((msg, index) => (
          <div key={index} className="mb-3">
            <strong>{msg.sender}:</strong>{" "}
            {msg.text}
          </div>
        ))}
      </div>

      <div className="flex gap-2 mt-4">
        <input
          className="border p-2 flex-1"
          value={message}
          onChange={(e) =>
            setMessage(e.target.value)
          }
          placeholder="Type a message..."
        />

        <button
          onClick={sendMessage}
          className="bg-blue-500 text-white px-4 py-2 rounded"
        >
          Send
        </button>
      </div>
    </div>
  );
}