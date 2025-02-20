import sys
import json
import modelMistral
try:
    description = sys.argv[1]

    if not isinstance(description, str):
        raise ValueError("Données description invalide")

    prompt = ("Estimez le prix d'un bien immobilier dans un json ('clé':'valeur') "
              "avec une fourchette de prix (clées prix_min et prix_max de type entier), "
              "un prix moyen par rapport au quartier/localisation (clée prix_moyen de type entier) "
              "et la confiance que l'on peut accorder à tes informations (clée confiance de type texte): "
             f"{description}")

    data = modelMistral.requestMistral(prompt)

    print(json.dumps({"data": data}))

except Exception as e:
    print(json.dumps({"error": str(e)}))
    sys.exit(1)
