<?php

namespace App\EGWK\Datamining\StorageDriver;

use App\EGWK\Datamining\StorageDriver;
use Illuminate\Support\Collection;

class Database implements StorageDriver
{

    const TRANSACTION_LIMIT = 100;

    protected $table = 'similarity_paragraph_work';

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function init()
    {
    }

    public function reset()
    {
        \DB::table($this->table)
            ->truncate();
    }

    public function store(Collection $data)
    {
        foreach ($data as $item) {
            try {
                \DB::table($this->table)
                    ->insert($item->toArray());
            } catch (\Illuminate\Database\QueryException $e) {
                // reverse para_id combination is considered duplicate, should be ignored.
            }
        }
    }

    function resetException()
    {
        // TODO: Implement resetException() method.
    }

    function storeException($data)
    {
        // TODO: Implement storeException() method.
    }

    function checkExistence($data): bool
    {
        return false;
    }

    function done()
    {
        // TODO: Implement done() method.
    }
}
