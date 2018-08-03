<?php

/*
  |--------------------------------------------------------------------------
  | Reader API Endpoints help
  |--------------------------------------------------------------------------
  |
  |
 */

/*
/
/books
/devotionals
/book/{code}
/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}
/chapter/{code}/{lang?}/{publisher?}/{year?}/{no?}
/metadata/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}
/metadata/chapter/{code}/{lang?}/{publisher?}/{year?}/{no?}
/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}
/parallel/{code}/{lang?}/{publisher?}/{year?}/{no?}
/paragraph/{refcode_short}/{lang?}/{publisher?}/{year?}/{no?}
/search
/trsearch
/similarity/{para_id}
/merge
 */

return [
    'writings' => [
        'books' => [
            'description' => 'List of translated books.',
            'uri' => '/books',
        ],
        'book' => [
            'description' => 'Getting paragraphs of a book.',
            'uri' => '/book/{code}',
            'uri_example' => '/book/DA',
            'uri_example_description' => 'Returns the paragraphs of Desire of Ages',
        ],
        'toc' => [
            'description' => 'Getting table of contents of a book.',
            'uri' => '/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/toc/PP',
            'uri_example_description' => 'Returns the table of contents of Patriarchs and Prophets with all Hungarian translations, chapter URIs provided.',
        ],
        'chapter' => [
            'description' => 'Getting table of contents of a book.',
            'uri' => '/chapter/{id}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/chapter/127.86/hu/advent',
            'uri_example_description' => 'Returns the paragraphs of The Acts of the Apostles, Chapter 3 - The Great Commission, with a Hungarian translation, published by the Advent Publishing House. URI provided by /toc entry point.',
        ],
        'translation' => [
            'description' => 'Getting paragraphs of a book with specified translation.',
            'uri' => '/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/translation/PP/hu/advent',
            'uri_example_description' => 'Returns the paragraphs of Patriarchs and Prophets with a Hungarian translation, published by the Advent Publishing House.',
        ],
        'parallel' => [
            'parallel' => 'The same as /translation, but includes non-translated paragraphs',
            'uri' => '/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/parallel/PP/hu/advent',
            'uri_example_description' => 'Returns the paragraphs of Patriarchs and Prophets with a Hungarian translation, including empty translations.',
        ],
        'paragraph' => [
            'description' => 'Query a paragraphs in the translated writings by reference code [BookCode Page.ParagraphNo]',
            'uri' => '/paragraph/refcode',
            'uri_example' => '/paragraph/ML 5.5',
            'uri_example_description' => 'Returns the paragraphs with all translations of My Life Today, p. 5.5',
        ],
    ],
    'metadata' => [
        'metadata_toc' => [
            'description' => 'Book metadata for table of contents.',
            'uri' => '/metadata/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/metadata/toc/PP',
            'uri_example_description' => 'Returns book metadata for the table of contents of Patriarchs and Prophets.',
        ],
        'metadata_chapter' => [
            'description' => 'Metadata for a chapter: book, section, chapter and navigation',
            'uri' => '/metadata/chapter/{id}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/metadata/chapter/127.86/hu/advent',
            'uri_example_description' => 'Returns metadata for The Acts of the Apostles, Chapter 3 - The Great Commission, with a Hungarian translation, published by the Advent Publishing House.',
        ],
    ],
    'search' => [
        'search' => [
            'description' => 'Searching in the writings.',
            'uri' => '/search?query=phrase',
            'uri_example' => '/search?query=Jesus+loves+to+have+us+come+to+Him',
            'uri_example_description' => 'Returns all paragraphs having the text: ``Jesus loves to have us come to Him´´.',
        ],
        'trsearch' => [
            'description' => 'Searching in the translated writings.',
            'uri' => '/trsearch?query=phrase',
            'uri_example' => '/trsearch?query=tekints fel és élj',
            'uri_example_description' => 'Returns all paragraphs having the text: ``tekints fel és élj´´.',
        ],
        'similarity' => [
            'description' => 'Search similar paragraphs by para_id',
            'uri' => '/similarity/{para_id}',
            'uri_example' => '/similarity/79.21',
            'uri_example_description' => 'Returns all paragraphs similar to 79.21 (that is My Life Today, p. 5.5).',
        ],
    ],
    'zip' => [
        'zip_book' => [
            'description' => 'Getting paragraphs of a book in zip format.',
            'uri' => '/zip/book/{code}',
            'uri_example' => '/zip/book/DA',
            'uri_example_description' => 'Returns the paragraphs of Desire of Ages',
        ],
        'zip_translation' => [
            'description' => 'Getting paragraphs of a book with specified translation in zip format.',
            'uri' => '/zip/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/zip/translation/PP/hu/advent',
            'uri_example_description' => 'Returns the paragraphs of Patriarchs and Prophets with a Hungarian translation, published by the Advent Publishing House.',
        ],
        'zip_paragraph' => [
            'description' => 'Query a paragraphs in the translated writings by reference code [BookCode Page.ParagraphNo] in zip format',
            'uri' => '/zip/paragraph/refcode',
            'uri_example' => '/zip/paragraph/ML 5.5',
            'uri_example_description' => 'Returns the paragraphs with all translations of My Life Today page 5, paragraph 5.',
        ],
    ],
];
