<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 02/12/2018
 * Time: 12:02
 */

namespace App\EGWK\Datamining;

use Illuminate\Support\Collection;

interface StorageDriver
{
    public function __construct($id);

    function init();

    function done();

    function reset();

    function store(Collection $data);

    function resetException();

    function storeException($data);

    function checkExistence($data): bool;
}
