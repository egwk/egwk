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
            'uri' => '/reader/books/{lang?}',
            'uri_example' => '/reader/books/hu',
            'uri_example_description' => 'Returns the list of Hungarian books',
        ],
        'book' => [
            'description' => 'Getting paragraphs of a book.',
            'uri' => '/reader/book/{code}',
            'uri_example' => '/reader/book/DA',
            'uri_example_description' => 'Returns the paragraphs of Desire of Ages',
        ],
        'toc' => [
            'description' => 'Getting table of contents of a book.',
            'uri' => '/reader/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/reader/toc/PP/hu',
            'uri_example_description' => 'Returns the table of contents of Patriarchs and Prophets with all Hungarian translations, chapter URIs provided.',
        ],
        'chapter' => [
            'description' => 'Getting table of contents of a book.',
            'uri' => '/reader/chapter/{id}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/chapter/127.86/hu/advent',
            'uri_example_description' => 'Returns the paragraphs of The Acts of the Apostles, Chapter 3 - The Great Commission, with a Hungarian translation, published by the Advent Publishing House. URI provided by /toc entry point.',
        ],
        'translation' => [
            'description' => 'Getting paragraphs of a book with specified translation.',
            'uri' => '/reader/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/reader/translation/PP/hu/advent',
            'uri_example_description' => 'Returns the paragraphs of Patriarchs and Prophets with a Hungarian translation, published by the Advent Publishing House.',
        ],
        'parallel' => [
            'parallel' => 'The same as /translation, but includes non-translated paragraphs',
            'uri' => '/reader/parallel/{code}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/reader/parallel/PP/hu/advent',
            'uri_example_description' => 'Returns the paragraphs of Patriarchs and Prophets with a Hungarian translation, including empty translations.',
        ],
        'paragraph' => [
            'description' => 'Query a paragraphs in the translated writings by reference code [BookCode Page.ParagraphNo]',
            'uri' => '/reader/paragraph/refcode',
            'uri_example' => '/reader/paragraph/ML 5.5',
            'uri_example_description' => 'Returns the paragraphs with all translations of My Life Today, p. 5.5',
        ],
    ],
    'metadata' => [
        'metadata_toc' => [
            'description' => 'Book metadata for table of contents.',
            'uri' => '/reader/metadata/toc/{code}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/reader/metadata/toc/PP',
            'uri_example_description' => 'Returns book metadata for the table of contents of Patriarchs and Prophets.',
        ],
        'metadata_chapter' => [
            'description' => 'Metadata for a chapter: book, section, chapter and navigation',
            'uri' => '/reader/metadata/chapter/{id}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/reader/metadata/chapter/127.86/hu/advent',
            'uri_example_description' => 'Returns metadata for The Acts of the Apostles, Chapter 3 - The Great Commission, with a Hungarian translation, published by the Advent Publishing House.',
        ],
    ],
    'search' => [
        'search' => [
            'description' => 'Searching in the writings.',
            'uri' => '/reader/search?query=phrase',
            'uri_example' => '/reader/search?query=Jesus+loves+to+have+us+come+to+Him',
            'uri_example_description' => 'Returns all paragraphs having the text: ``Jesus loves to have us come to Him´´.',
        ],
        'trsearch' => [
            'description' => 'Searching in the translated writings.',
            'uri' => '/reader/search/translation?query=phrase',
            'uri_example' => '/reader/search/translation?query=tekints fel és élj',
            'uri_example_description' => 'Returns all paragraphs having the text: ``tekints fel és élj´´.',
        ],
        'similarity' => [
            'description' => 'Search similar paragraphs by para_id',
            'uri' => '/reader/search/similarity/{para_id}',
            'uri_example' => '/reader/search/similarity/79.21',
            'uri_example_description' => 'Returns all paragraphs similar to 79.21 (that is My Life Today, p. 5.5).',
        ],
        'cluster' => [
            'description' => 'Search phrase, cluster results by similarity',
            'uri' => '/reader/search/cluster?query=phrase[&cover=percent|&covers=percent&covered=percent][&reference=para_id]',
            'uri_example' => '/reader/search/cluster?query=Jesus+loves+to&cover=60',
            'uri_example_description' => 'Returns all paragraphs having the text: ```Jesus loves to´´, clustered by 60% coverage to both directions.',
        ],
    ],
    'zip' => [
        'zip_book' => [
            'description' => 'Getting paragraphs of a book in zip format.',
            'uri' => '/reader/zip/book/{code}',
            'uri_example' => '/reader/zip/book/DA',
            'uri_example_description' => 'Returns the paragraphs of Desire of Ages',
        ],
        'zip_translation' => [
            'description' => 'Getting paragraphs of a book with specified translation in zip format.',
            'uri' => '/reader/zip/translation/{code}/{lang?}/{publisher?}/{year?}/{no?}',
            'uri_example' => '/reader/zip/translation/PP/hu/advent',
            'uri_example_description' => 'Returns the paragraphs of Patriarchs and Prophets with a Hungarian translation, published by the Advent Publishing House.',
        ],
        'zip_paragraph' => [
            'description' => 'Query a paragraphs in the translated writings by reference code [BookCode Page.ParagraphNo] in zip format',
            'uri' => '/reader/zip/paragraph/refcode',
            'uri_example' => '/reader/zip/paragraph/ML 5.5',
            'uri_example_description' => 'Returns the paragraphs with all translations of My Life Today page 5, paragraph 5.',
        ],
    ],
];
