<?php

namespace App\Console\Commands\Export\Translation;

class Html extends Txt
{

    protected $signature = 'export:html' . self::SIGNATURE_SUFFFIX;
    protected $description = 'Exports book as html .html';

    protected function refcode($item)
    {
        return $this->ids && !empty($item->refcode_short) ? ' <span class="egw_refcode" title="' . $item->refcode_long . '">{' . $item->refcode_short . '}</span>' : '';
    }

    public function getOriginal($item)
    {
        return '<div class="egw_content_container">' . $item->content . $this->refcode($item) . '</div>';

    }

    protected function writeFile($content, $folder, $filename, $ext = '.txt')
    {
        parent::writeFile(
            collect(['<html>', '<body>'])
                ->concat($content)
                ->concat(['</body>', '</html>'])
            ,
            $folder,
            $filename, '.html'
        );
    }

}
