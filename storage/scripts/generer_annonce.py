import sys
import json
import modelMistral as modelAI

try:
    requestValidated = json.loads(sys.argv[1])

    required_keys = {"type", "surface", "pieces", "ville"}
    if not required_keys.issubset(requestValidated.keys()):
        raise ValueError("Données JSON invalides ou incomplètes.")

    for key in required_keys:
        if not requestValidated[key]:
            raise ValueError(f"La valeur de '{key}' est vide ou invalide.")

    prompt = (f"Générez une description optimisée et courte sans style pour un bien immobilier de type {requestValidated['type']}, "
             f"surface de {requestValidated['surface']} m², avec {requestValidated['pieces']} pièces, situé à {requestValidated['ville']}.")

    data = modelAI.requestModelAI(prompt)

    print(json.dumps({"data": data}))

except json.JSONDecodeError:
    print(json.dumps({"error": "Erreur de décodage JSON. Assurez-vous que les données fournies sont au format JSON valide."}))
    sys.exit(1)
except Exception as e:
    print(json.dumps({"error": str(e)}))
    sys.exit(1)
