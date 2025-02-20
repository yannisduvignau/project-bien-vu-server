import os
from openai import OpenAI
from dotenv import load_dotenv

# Chargement des variables d'environnement
env_path = os.path.abspath(os.path.join(os.path.dirname(__file__), "../..", ".env"))
if os.path.exists(env_path):
    load_dotenv(env_path)

# Récupération et validation de la clé API
api_key = os.getenv("OPENAI_API_KEY", "").strip()
if not api_key:
    raise ValueError("La clé API OPENAI_API_KEY est introuvable ou vide. Vérifiez le fichier .env.")

# Initialisation du client OpenAI
client = OpenAI(api_key=api_key)

# Modèle utilisé
MODEL_NAME = "gpt-3.5-turbo"

def requestModelAI(prompt):
    """Envoie une requête au modèle OpenAI et retourne la réponse."""
    try:
        completion = client.chat.completions.create(
            model=MODEL_NAME,
            messages=[
                {"role": "system", "content": "You are an expert real estate agent."},
                {"role": "user", "content": prompt}
            ]
        )

        # Vérification de la réponse
        if not completion or not completion.choices:
            raise ValueError("Réponse invalide de l'API OpenAI.")

        return completion.choices[0].message.content.strip()

    except Exception as e:
        raise RuntimeError(f"Erreur lors de la requête OpenAI : {str(e)}")

