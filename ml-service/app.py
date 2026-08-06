from fastapi import FastAPI
from pydantic import BaseModel
import pickle
import pandas as pd
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity


app = FastAPI()


# Load ML files
vectorizer = pickle.load(open("vectorizer.pkl", "rb"))
df = pd.read_pickle("conversation_data.pkl")

# Convert questions to vectors
X = vectorizer.transform(df["question"])


class ChatRequest(BaseModel):
    message: str


@app.get("/")
def home():
    return {
        "message": "ML Chatbot API is running"
    }


@app.post("/chat")
def chat(data: ChatRequest):

    user_message = data.message

    message_vector = vectorizer.transform([user_message])

    similarity = cosine_similarity(message_vector, X)

    score = similarity.max()

    index = np.argmax(similarity)

    if score < 0.3:
        answer = "Sorry, I don't understand."
    else:
        answer = df.iloc[index]["answer"]

    return {
        "response": answer
    }