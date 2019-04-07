# EGWK commandline tools

##`Installer` commands

#### Download original
Signature: `download:writings {--s|skipto=} {--t|titleonly}`

Downloads Ellen White writings, and dumps books into csv file.
    
### `Draft` commands

#### Import draft
Signature: `import:draft {--f|file=} {--s|skipempty} {--x|noexport}`

Imports translation drafts (`.txt` files) into the database

#### Approve draft
Signature: `approve:draft {--f|file=} {--c|cleanup} {--x|noexport} {--r|refreshcache}`

Approves translation drafts by copying them to the live `Translations` table.

#### Export draft 
Signature: `export:draft {--f|file=} {--p|parallel}`

Export translation draft into text file.

`--file` is the draft "id", the filename (without path and `.txt` extension) of the text file the draft was imported

`--parallel` switch adds original paragraphs `tab` separated. 

##`Datamining` commands

### Text similarity mining

#### Paragraph similarity
Signature: `similarity:paragraph {--s|startid=0} {--l|limit=0} {--o|offset=0} {--f|output=ParagraphSimilarity}`

#### Bible text mining
Signature: `mine:bible`

Mining Bible quotes in writings

##`Export` commands

Collection of `artisan` `CLI` commands to export database content into various formats.

The export target folder is defined by the `STORAGE_PATH` variable, set in `.env`. This is set in `config/filesystems.php` and is used by `\Storage::path()`:

```php
    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => env('STORAGE_PATH', storage_path('app')),
        ],
```

###  Bible text export commands
####  Bible text export
Signature: `export:bible {translations*} {--c|cleanup}`

Exports Bible translation to plain txt format

Usage example: `$ php artisan export:bible KJV RV KÁROLI`
The above command will export the King James Bible, the Revised Version and the Hungarian Károli translation.

`--cleanup` will cleanup Strong numbers and other tags from the text.

#### <a name="bible-n-grams-export"></a>Bible n-grams export
Signature: `export:bible:ngrams {translations*}`

Exports Bible translation n-grams into `TSV` files. Translations are concatenated into a single file: `\Storage::path('bible/ngrams.txt')`

Filters applied:
- normalization (lowercase alphanumerical characters only) 
- with and without stopwords
- English lemmaziter
- Single verse - signle n-gram occurrence

`TSV` format:

```text
index, version, book_id, chapter, verse, stopwords, word_count_filtered, word_count, window_index, word_limit, ngram
```

**WARNING:** it generates large (>1GB) output file!

**NOTE:** the output file is **used by Sphinx server**, for generating `bible_ngrams` source and `i_bible_ngrams` index.

#### Filtering  Bible "stop n-grams"
Signature: `ngrams:bible:filter`

Filters Bible "stop n-grams", removes 

**NOTE:** running filter command **requires Sphinx service**, and `i_bible_ngrams` index (see: [Bible n-grams export](#bible-n-grams-export)). 


###  Publication export commands
#### Text export
Signature: `export:txt {books*} {--l|language=hu} {--o|original=translation} {--p|publisher=} {--y|year=} {--u|no=} {--i|ids}`

Exports book as text `.txt`

####  Exports books in original language
Signature: `export:original {books*}`

See also: `export:txt {books*} --original=original`

Exports original language book as text `.txt`

####  Filtered export
Signature: `export:original:filtered {books*}`

Exports original books into filtered TSV files 

Filters applied:
- normalization (lowecase alphanumerical characters only) 
- with and without stopwords
- English lemmaziter

#### Export compilation
Signature: `compile:[docx|json] {books*} {--l|language=hu} {--t|threshold=70} {--m|multitranslation} {--p|publisher=}`

Exports compiled 'translation' Ms Word `.docx` or in `.json` format.

Compiled 'translation' is a fake translation of a given publication to specific language. It's a compilation of similar paragraphs translated in other translated publications.  
