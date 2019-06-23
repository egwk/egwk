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

    public function create(string $name, Builder $builder, $fromCache = true)
    {
        //
        // @todo: more clever temp file generation
        //
        $filename = "/tmp/$name.zip";
        if ($fromCache && file_exists($filename)) {
            return $filename;
        }
        $data = $builder->get();
        $zip = new \ZipArchive();

        if ($zip->open($filename, \ZipArchive::CREATE) !== true) {
            return false;
        }
        $zip->addFromString("$name.json", $data);
        $zip->close();
        return $filename;
    }

    public function createFromFile(string $path, $tempFile = null, $fromCache = true)
    {
        $tempFile = $tempFile ?: "/tmp/" . basename($path) . ".zip";
        if ($fromCache && file_exists($tempFile)) {
            return $tempFile;
        }
        $zip = new \ZipArchive();
        if ($zip->open($tempFile, \ZipArchive::CREATE) !== true) {
            return false;
        }
        $zip->addFile($path, basename($path));
        $zip->close();
        return $tempFile;
    }

    public function header(string $name)
    {
        return array_merge(
            $this->header, ["Content-Disposition" => "attachment; filename=\"$name.zip\""]
        );
    }

}
