<?php

return [
    'analyser' => [
        'description' => [
            'required' => 'La description est requise pour procèder à une analyse d\'annonce.',
            'string' => 'La description de l\'annonce doit etre de type texte pour pouvoir être analysée.',
        ],
    ],
    'estimer' => [
        'description' => [
            'required' => 'La description est requise pour procèder à une estimation d\'annonce.',
            'string' => 'La description de l\'annonce doit etre de type texte pour pouvoir être estimée.',
        ],
    ],
    'generer' => [
        'type' => [
            'required' => 'Le type de bien est requis pour générer une annonce.',
            'string' => 'Le type de bien doit être une chaîne de caractères.',
            'max' => 'Le type de bien ne doit pas dépasser :max caractères.',
        ],
        'surface' => [
            'required' => 'La surface du bien est requise pour générer une annonce.',
            'numeric' => 'La surface du bien doit être un nombre.',
            'min' => 'La surface du bien doit être d\'au moins :min m².',
        ],
        'pieces' => [
            'required' => 'Le nombre de pièces est requis pour générer une annonce.',
            'integer' => 'Le nombre de pièces doit être un nombre entier.',
            'min' => 'Le bien doit avoir au moins :min pièce(s).',
        ],
        'ville' => [
            'required' => 'Le nom de la ville est requis pour générer une annonce.',
            'string' => 'Le nom de la ville doit être une chaîne de caractères.',
            'max' => 'Le nom de la ville ne doit pas dépasser :max caractères.',
        ],
    ],

    'scraping' => [
        'url' => [
            'required' => 'L\'url est requise pour procèder à la récupération des informations de l\'annonce.',
            'url' => 'L\'url de l\'annonce doit etre de type url pour pouvoir procèder à la récupération des informations.',
        ],
    ],
];
