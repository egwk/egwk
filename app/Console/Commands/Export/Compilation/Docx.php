<?php

namespace App\Console\Commands\Export\Compilation;

use App\Console\Commands\Export\Compile;
use Facades\App\EGWK\Translation\CompileBook;
use Illuminate\Support\Facades\Storage;

class Docx extends Compile
{

    const baseFolder = 'compilations/docx';

    protected $signature = 'compile:docx' . self::SIGNATURE_SUFFFIX;
    protected $description = 'Compiles book as Ms Word .docx';

    protected function setupPhpWord($book, $collection)
    {
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $properties = $phpWord->getDocInfo();
        $properties->setCreator('EGWK');
        $properties->setCompany('Ellen Gould White Könyvtár');
        $properties->setTitle($book);
        $properties->setDescription($book);
        $collection and $properties->setCategory($collection);
        $properties->setCreated(time());
        $properties->setModified(time());
        $properties->setSubject('Auto-translation parallel view');
        $properties->setKeywords('');
        return $phpWord;
    }


    protected function convertText($content)
    {
        return html_entity_decode(strip_tags($content), ENT_QUOTES | ENT_HTML5);
    }

    protected function refCode($content)
    {
        return "{{$content}}";
    }

    protected function flatten($similars)
    {
        $s = [];
        foreach ($similars as $similar) {
            foreach ($similar->translations as $translation) {
                $s[] = (object)[
                    'para_id' => $translation->para_id,
                    'content' => $translation->content,
                    'refcode_short' => $similar->paragraph->refcode_short,
                    'lang' => $translation->lang,
                    'publisher' => $translation->publisher,
                    'year' => $translation->year,
                    'covers' => $similar->covers,
                    'covered' => $similar->covered,
                    'priority' => $translation->priority,
               ];
            }
        }
        return collect($s)
            ->filter(function ($value, $key) {
                return !empty(trim($value->content));
            })
            ->slice(0,6)
            ->sortBy('priority');
    }

    /**
     * Export
     *
     * @return mixed
     */
    protected function compile($book, $collection, $threshold = 70, $multiSimilar = false, $multiTranslation = false, $language = null)
    {

        $folder = Storage::path(static::baseFolder . ($collection ? "/$collection" : ''));
        @mkdir($folder);

        $phpWord = $this->setupPhpWord($book, $collection);
        $table = $phpWord->addSection()->addTable();

        foreach (CompileBook::translate(
            $book,
            $threshold,
            $multiSimilar,
            $multiTranslation,
            $language
        ) as $paragraph) {
            $table->addRow();
            $cell = $table->addCell(5000);
            $content = $this->convertText($paragraph->paragraph->content) .
                ' ' .
                $this->refCode($paragraph->paragraph->refcode_short);
            $cell->addText($content);
            $cell = $table->addCell(5000, ['lang' => 'hu-HU']); // todo: doesn't work yet.
            foreach ($this->flatten($paragraph->similars) as $similar) {
                $content = $this->convertText($similar->content) .
                    ' ' .
                    $this->refCode($similar->refcode_short);
                $cell->addText($content);
                $textrun = $cell->addTextRun();
                $textrun->addText(
                    $similar->covers . '% ',
                    [
                        'bold' => true,
                        'size' => 7.5
                    ]);
                $textrun->addText('/ ' . $similar->covered . '% ', ['size' => 7.5]);
                $textrun->addText('(' . $similar->para_id . ') ', ['size' => 7.5]);
                $textrun->addText($similar->lang . '/' . $similar->publisher . '/' . $similar->year, ['size' => 7.5]);

                $cell->addTextBreak();
            }
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save("$folder/$book.docx");
    }
}
