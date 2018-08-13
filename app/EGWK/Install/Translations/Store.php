<?php

namespace App\EGWK\Install\Translations;

/**
 * Stores translation data
 *
 * @author Peter
 */
interface Store
{

    /**
     * Stores translation metadata
     * 
     * @param \stdClass $metadata Translation metadata
     */
    public function metadata(\stdClass $metadata);

    /**
     * Stores translation metadata
     * 
     * @param array $records Records
     */
    public function records(array $records);
}
