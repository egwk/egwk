<?php

namespace App\Console\Commands;

use Facades\App\EGWK\Translation\CompileBook;
use Illuminate\Support\Facades\Storage;

class ExportDocx extends Export
{
    protected $signature = 'export:docx' . self::SIGNATURE_SUFFFIX;
    protected $description = 'Exports book as Ms Word .docx';

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

    /**
     * Export
     *
     * @return mixed
     */
    protected function export($book, $collection, $threshold = 70, $multiTranslation = false, $language = null)
    {

        $folder = Storage::path('compilations/docx' . ($collection ? "/$collection" : ''));
        @mkdir($folder);

        $phpWord = $this->setupPhpWord($book, $collection);
        $table = $phpWord->addSection()->addTable();

        foreach (CompileBook::translate(
            $book,
            $threshold,
            $multiTranslation,
            $language
        ) as $paragraph) {
            $table->addRow();
            $cell = $table->addCell(5000);
            $content = $this->convertText($paragraph->paragraph->content) .
                ' ' .
                $this->refCode($paragraph->paragraph->refcode_short);
            $cell->addText($content);
            $cell = $table->addCell(5000);
            foreach ($paragraph->similars as $similar) {
                foreach ($similar->translations as $translation) {
                    $content = $this->convertText($translation->content) .
                        ' ' .
                        $this->refCode($similar->paragraph->refcode_short);
                    $cell->addText($content);
                    $textrun = $cell->addTextRun();
                    $textrun->addText(
                        $similar->covers . '% ',
                        [
                            'bold' => true,
                            'size' => 7.5
                        ]);
                    $textrun->addText('/ ' . $similar->covered . '% ', ['size' => 7.5]);
                    $textrun->addText('(' . $translation->para_id . ') ', ['size' => 7.5]);
                    $textrun->addText($translation->lang . '/' . $translation->publisher . '/' . $translation->year, ['size' => 7.5]);

                    $cell->addTextBreak();
                }
            }
        }

        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWriter->save("$folder/$book.docx");
    }
}
