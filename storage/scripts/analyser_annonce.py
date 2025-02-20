import sys
import json
import modelMistral
try:
    description = sys.argv[1]

    if not isinstance(description, str):
        raise ValueError("Données description invalide")

    prompt = ("Analysez cette annonce immobilière dans un json ('clé':'valeur') "
              "en déterminant la coherence (clée coherence), "
              "les informations manquantes pour faire une bonne annonce (clée empty_key), "
              "les erreurs (clée erreurs) "
              "et sa fiabilité (clée fiabilite): "
             f"{description}")

    data = modelMistral.requestMistral(prompt)

    print(json.dumps({"data": data}))

except Exception as e:
    print(json.dumps({"error": str(e)}))
    sys.exit(1)
