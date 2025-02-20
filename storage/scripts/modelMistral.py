import os
from mistralai import Mistral
from dotenv import load_dotenv

env_path = os.path.abspath(os.path.join(os.path.dirname(__file__),"../..", ".env"))
if os.path.exists(env_path):
    load_dotenv(env_path)

# Récupération de la clé API
api_key = os.getenv("MISTRAL_API_KEY")
if not api_key:
    raise ValueError("La clé API MISTRAL_API_KEY est introuvable. Vérifiez le fichier .env.")

def getClient():
 return Mistral(api_key=api_key)

def getModel():
 return "mistral-large-latest"

def requestMistral(prompt):
    model = getModel()
    client = getClient()

    response = client.chat.complete(
        model=model,
        messages=[{"role": "user", "content": prompt}]
    )

    if not response or not response.choices:
        raise ValueError("Réponse invalide de l'API Mistral.")

    return response.choices[0].message.content
