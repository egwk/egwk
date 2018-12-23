<?php

namespace App\EGWK\Datamining\StorageDriver;

use App\EGWK\Datamining\StorageDriver;
use Illuminate\Support\Collection;

class Database implements StorageDriver
{

    const TRANSACTION_LIMIT = 1000;

    protected $table = 'similarity_paragraph_work';

    protected $transactionCounter = 0;

    protected $inserts = [];

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
        $this->transactionCounter++;
        $this->inserts[] = $data;
        if ($this->transactionCounter >= self::TRANSACTION_LIMIT) {
            \DB::transaction(function () {
                foreach ($this->inserts as $data) {
                    \DB::table($this->table)
                        ->insert($data->toArray());
                }
            });
            $this->transactionCounter = 0;
            $this->inserts = [];
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
