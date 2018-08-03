<?php

namespace App\Tools;

use Illuminate\Database\Eloquent\Builder;

/**
 * Description of ZipJson
 *
 * @author Peter
 */
class ZipJson
    {

    protected $header = [
        "Pragma" => "public",
        "Expires" => "0",
        "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
        "Cache-Control" => "public",
        "Content-Description" => "File Transfer",
        "Content-type" => "application/octet-stream",
        "Content-Transfer-Encoding" => "binary",
    ];

    public function create(string $name, Builder $builder)
        {
        //
        // @todo: more clever temp file generation
        //
        $data = $builder->get();
        $zip = new \ZipArchive();
        $filename = "/tmp/$name.zip";
        if ($zip->open($filename, \ZipArchive::CREATE) !== true)
            {
            return false;
            }
        $zip->addFromString("$name.json", $data);
        $zip->close();
        return $filename;
        }

    public function header(string $name)
        {
        return array_merge(
            $this->header, ["Content-Disposition" => "attachment; filename=\"$name.zip\""]
        );
        }

    }
