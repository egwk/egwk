<?php

/*
  |--------------------------------------------------------------------------
  | Reader API Endpoints help
  |--------------------------------------------------------------------------
  |
  |
 */

return [
    'hymnals' => [
        'description' => 'List of hymnals',
        'uri' => '/hymnals/{lang?}',
        'uri_example' => '/hymnals',
        'uri_example_description' => 'Returns the list of hymnals on all languages, or on a given language.',
    ],
    'languages' => [
        'description' => 'List of hymnal languages',
        'uri' => '/hymnals/languages',
        'uri_example' => '/hymnals/languages',
        'uri_example_description' => 'Returns the list of hymnal languages.',
    ],
    'hymnal' => [
        'description' => 'List of hymns',
        'uri' => '/hymnal/{slug}',
        'uri_example' => '/hymnal/sda-hymnal',
        'uri_example_description' => 'Returns the complete TOC of the Seventh-Day Adventist Hymnal.',
    ],
    'hymn' => [
        'description' => 'Hymn verses',
        'uri' => '/hymn/{slug}/{no}',
        'uri_example' => '/hymn/sda-hymnal/92',
        'uri_example_description' => 'Returns all verses of the hymn: "This is my Father’s world".',
    ],
    'verse' => [
        'description' => 'Single hymn verse',
        'uri' => '/hymn/{slug}/{no}/{verse}',
        'uri_example' => '/hymn/nuevo-himnario-adventista/333/2',
        'uri_example_description' => 'Returns the 2nd verse of the hymn: "Más allá del sol", starting with "Así por el mundo..." from the Spanish hymnal, "Nuevo Himnario Adventista".',
    ],
    'translations' => [
        'hymn' => [
            'description' => 'Hymn translation',
            'uri' => '/hymn/translate/all/{slug}/{no}',
            'uri_example' => '/hymn/translate/all/hitunk-enekei/157',
            'uri_example_description' => 'Returns all translations of the hymn: "Isten Lelke: égi fény", eg. "Santo Espíritu de Dios" in Spanish and "Saint-Esprit" in French.',
        ],
        'verse' => [
            'description' => 'Single hymn verse translation',
            'uri' => '/hymn/translate/all/{slug}/{no}/{verse}',
            'uri_example' => '/hymn/translate/all/hitunk-enekei/157/1',
            'uri_example_description' => 'Returns all translations of the first verse of the hymn: "Isten Lelke: égi fény", eg. "Santo Espíritu de Dios" in Spanish and "Saint-Esprit" in French.',
        ],
        'hymn_lang' => [
            'description' => 'Hymn translation',
            'uri' => '/hymn/translate/{lang}/{slug}/{no}',
            'uri_example' => '/hymn/translate/fr/hitunk-enekei/157',
            'uri_example_description' => 'Returns the French translation of the hymn "Isten Lelke: égi fény" - "Saint-Esprit"',
        ],
        'verse_lang' => [
            'description' => 'Single hymn verse translation',
            'uri' => '/hymn/translate/{lang}/{slug}/{no}/{verse}',
            'uri_example' => '/hymn/translate/fr/hitunk-enekei/157/1',
            'uri_example_description' => 'Returns the French translation of the first verse of the hymn "Isten Lelke: égi fény" - "Saint-Esprit"',
        ],
    ],
];
