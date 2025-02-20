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
    'scraping' => [
        'url' => [
            'required' => 'L\'url est requise pour procèder à la récupération des informations de l\'annonce.',
            'url' => 'L\'url de l\'annonce doit etre de type url pour pouvoir procèder à la récupération des informations.',
        ],
    ],
];
