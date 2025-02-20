import sys
import os
import json
import modelOpenAI as modelAIOpenAI
# import modelMistral as modelAIMistral
from dotenv import load_dotenv

env_path = os.path.abspath(os.path.join(os.path.dirname(__file__), "../..", ".env"))
if os.path.exists(env_path):
    load_dotenv(env_path)

# Récupération et validation de la clé API
PROMPT_ANALYSER = os.getenv("PROMPT_ANALYSER", "").strip()
if not PROMPT_ANALYSER:
    raise ValueError("La variable PROMPT_ANALYSER est introuvable ou vide. Vérifiez le fichier .env.")

try:
    description = sys.argv[1]

    if not isinstance(description, str):
        raise ValueError("Données description invalide")

    prompt = f"{PROMPT_ANALYSER} {description}"

    data = modelAIOpenAI.requestModelAI(prompt)

except Exception as e:
    print(json.dumps({"error": str(e)}))
    sys.exit(1)
    # data = modelAIMistral.requestModelAI(prompt)

print(json.dumps({"data": data}))
