<?php

/**
 * Convert a multi-dimensional, associative array to CSV data
 *
 * https://coderwall.com/p/zvzwwa/array-to-comma-separated-string-in-php
 *
 * @param  array $data the array of data
 * @return string       CSV text
 */
function str_putcsv($data)
{
    // Generate CSV data from array
    $fh = fopen('php://temp', 'rw'); // don't create a file, attempt
    // to use memory instead

    // write out the headers
    fputcsv($fh, array_keys(current($data)));

    // write out the data
    foreach ($data as $row) {
        fputcsv($fh, $row);
    }
    rewind($fh);
    $csv = stream_get_contents($fh);
    fclose($fh);

    return $csv;
}

function sphinx()
{
    $connection = new \Foolz\SphinxQL\Drivers\Pdo\Connection();
    $driver = config('scout.driver', 'sphinxsearch');
    $connection->setParams(['host' => config('scout.' . $driver . '.host', 'sphinx'), 'port' => config('scout.' . $driver . '.port', 9306)]);
    return new \Foolz\SphinxQL\SphinxQL($connection);
}
