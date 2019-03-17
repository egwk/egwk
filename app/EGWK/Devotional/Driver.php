<?php
/**
 * Created by PhpStorm.
 * User: peter_erdodi
 * Date: 17/03/2019
 * Time: 17:37
 */

namespace App\EGWK\Devotional;


interface Driver
{

    /**
     * Driver constructor.
     * @param string $id
     */
    public function __construct(string $id);

    /**
     * @return array
     */
    public function all(): array;

    /**
     * @param string|null $year
     * @return array
     */
    public function year(string $year = null): array;

    /**
     * @return array
     */
    public function today(): array;

    /**
     * @param string $date
     * @return array
     */
    public function date(string$date): array;

}
