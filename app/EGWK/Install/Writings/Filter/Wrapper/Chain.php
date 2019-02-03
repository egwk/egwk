<?php

namespace App\EGWK\Install\Writings\Filter\Wrapper;

use App\EGWK\Install\Writings\Filter;

/**
 * Filter wrapper class
 *
 * @author Peter
 */
class Chain
{

    /**
     *
     * @var mixed Data under filtering
     */
    protected $data = null;

    /**
     *
     * @var Filter Filter 
     */
    protected $filter;

    /**
     * Class constructor
     *
     * @access public
     * @param Filter $filter Filter object
     * @return void
     */
    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    /**
     * Gets Filter object
     *
     * @access public
     * @return Filter
     */
    public function getFilter(): Filter
    {
        return $this->filter;
    }

    /**
     * Gets data
     *
     * @access public
     * @return mixed Data under filtering
     */
    public function get()
    {
        return $this->data;
    }

    /**
     * Sets data
     *
     * @access public
     * @param mixed $data Data under filtering
     * @return Chain
     */
    public function set($data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * Data LENGTH
     *
     * @access public
     * @return integer
     */
    public function length()
    {
        return is_array($this->data) ? count($this->data) :
            (is_string($this->data) ? strlen($this->data) : 0);
    }

    /**
     * Filter call magic method
     *
     * @access public
     * @param string $methodName Method name
     * @param array $args Arguments
     * @return Chain
     */
    public function __call($methodName, $args)
    {
        if (is_callable([$this->filter, $methodName]))
        {
            $this->data = $this->filter->{$methodName}($this->data);
        }
        return $this;
    }

}
