<?php

namespace App\EGWK\Datamining\StorageDriver;


use App\EGWK\Datamining\StorageDriver;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class File implements StorageDriver
{
    const BUFFER_LIMIT = 50;

    const OUTPUT_PATH = 'datamine/csv/';

    /**
     * @var string
     */
    protected $filePath = '';

    /**
     * @var string
     */
    protected $exceptionFilePath = '';

    /**
     * @var Collection
     */
    protected $dataBuffer;

    protected function prepareFiles()
    {
        Storage::makeDirectory(self::OUTPUT_PATH);
        Storage::delete([$this->filePath, $this->exceptionFilePath]);
    }

    protected function saveToFile($data, $filePath = null)
    {
        $filePath = $filePath ?: $this->filePath;
        if (!empty(trim($data))) {
            file_put_contents(Storage::path($filePath), trim($data) . "\n", FILE_APPEND);
        }
    }

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function init()
    {
        $this->filePath = self::OUTPUT_PATH . $this->id . '.csv';
        $this->exceptionFilePath = self::OUTPUT_PATH . $this->id . '.exceptions.csv';
        Storage::makeDirectory(self::OUTPUT_PATH);
        $this->reset();
        $this->resetException();
        $this->resetBuffer();
    }

    public function done()
    {
        $this->flushBuffer();
    }

    public function reset()
    {
        Storage::delete($this->filePath);
    }

    protected function timetoFlush()
    {
        return $this->dataBuffer->count() >= self::BUFFER_LIMIT;
    }

    protected function addToBuffer($data)
    {
        $this->dataBuffer->push($data);
    }

    protected function resetBuffer()
    {
        $this->dataBuffer = collect([]);
    }

    protected function flushBuffer()
    {
        $this->saveToFile(
            $this->dataBuffer
                ->map(function ($bufferData) {
                    return $bufferData
                        ->map(
                            function ($e) {
                                return $e->implode(',');
                            })
                        ->implode("\n");
                })
                ->implode("\n")
        );
        $this->resetBuffer();
    }

    public function store(Collection $data)
    {
        if ($data->count() > 0) {
            $this->addToBuffer($data);
            if ($this->timetoFlush()) {
                $this->flushBuffer();
            }
        }
    }

    function resetException()
    {
        Storage::delete($this->exceptionFilePath);
    }

    function storeException($data)
    {
        $this->saveToFile($data, $this->exceptionFilePath);
    }

    function checkExistence($data): bool
    {
        return false;
    }
}
