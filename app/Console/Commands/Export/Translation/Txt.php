<?php

namespace App\Console\Commands\Export\Translation;

use App\Facades\Reader;
use App\Console\Commands\Export\Translation;

class Txt extends Translation
{
    protected $signature = 'export:txt' . self::SIGNATURE_SUFFFIX;
    protected $description = 'Exports book as text .txt';
    protected $ids = false;

    protected function refcode($item)
    {
        return $this->ids && !empty($item->refcode_short) ? ' {' . $item->refcode_short . '}' : '';
    }

    public function getTranslation($item)
    {
        $tmp = $item->translations->first();
        $translation = $tmp !== null ? $tmp->content : '';

        return str_replace("\n", '<br/>', $translation) . $this->refcode($item);

    }

    public function getOriginal($item)
    {
        return html_entity_decode(strip_tags($item->content)) . $this->refcode($item);

    }

    /**
     * Export
     *
     * @return mixed
     */
    protected function export($book, $collection, $language = 'hu', $original = 'translation', $publisher = null, $year = null, $no = null, $ids = false)
    {
        $this->ids = $ids;
        $me = $this;

        $folder = 'exported' . ($collection ? "/$collection" : '');

        $content = null;
        switch ($original) {
            case 'original': // original text only
            case 'o':
                $original = 'original';
                $content = Reader::original($book)
                    ->get()
                    ->map([$this, 'getOriginal']);
                break;
            case 'draft': // including empty lines
            case 'd':
                $original = 'draft';
                $content = Reader::parallel($book, $language, $publisher, $year, $no)
                    ->get()
                    ->map([$this, 'getTranslation']);
                break;
            case'parallel': // with translation
            case 'p':
                $original = 'parallel';
                $content = Reader::parallel($book, $language, $publisher, $year, $no)
                    ->get()
                    ->map(function ($item) use ($me) {

                        return
                            $me->getOriginal($item)
                            . "\t"
                            . $me->getTranslation($item);
                    });
                break;
            case 'translation': // translated paragraphs only
            case 't':
            default:
                $original = 'translation';
                $content = Reader::translations($book, $language, $publisher, $year, $no)
                    ->get()
                    ->map([$this, 'getTranslation']);
                break;
        }

        $id = implode('.', array_filter([$language, $publisher, $year, $no]));

        \Storage::put(
            "$folder/$book.$original.$id.txt",
            $content->implode("\n")
        );
    }
}
