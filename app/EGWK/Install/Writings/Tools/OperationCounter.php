<?php

namespace App\EGWK\Install\Writings\Tools;

/**
 * Adds limitable operation step counter to a class
 * 
 * @author Peter
 */
trait OperationCounter
{

    /**
     *
     * @var int Operation Counter
     */
    private $operationCounter;

    /**
     *
     * @var int Operation Limit
     */
    private $operationLimit;

    /**
     *
     * @var bool Operation Term Signal
     */
    private $operationTermSignal;

    /**
     * Init Counter No Limit
     *
     * @access private
     * @return void
     */
    private function initCounterNoLimit()
    {
        $this->initCounter(0);
    }

    /**
     * Init Counter with Limit
     *
     * @access private
     * @param int $limit Limit
     * @return void
     */
    private function initCounter(int $limit)
    {
        $this->resetCounter();
        $this->setCounterLimit($limit);
    }

    /**
     * Sets Operation Term Signal state
     *
     * @access private
     * @param bool $operationTermSignal Operation Term Signal
     * @return void
     */
    private function setOperationTermSignal($operationTermSignal = false)
    {
        $this->operationTermSignal = $operationTermSignal;
    }

    /**
     * Gets Operation Term Signal state
     *
     * @access private
     * @return bool Operation Term Signal state
     */
    private function getOperationTermSignal()
    {
        return $this->operationTermSignal;
    }

    /**
     * Sets Counter
     *
     * @access private
     * @param int $operationCounter Operation Counter
     * @return void
     */
    private function setCounter($operationCounter = 0)
    {
        $this->operationCounter = $operationCounter;
    }

    /**
     * Increases Counter
     *
     * @access private
     * @return void
     */
    private function increaseCounter()
    {
        $this->operationCounter++;
    }

    /**
     * Resets Counter
     *
     * @access private
     * @return void
     */
    private function resetCounter()
    {
        $this->setCounter(0);
        $this->setOperationTermSignal(false);
    }

    /**
     * Sets Operation Counter Limit
     *
     * @access private
     * @param int $limit Operation limit
     * @return void
     */
    private function setCounterLimit(int $limit)
    {
        $this->operationLimit = $limit;
    }

    /**
     * Sends Term Signal
     *
     * @access private
     * @return void
     */
    private function sendTermSignal()
    {
        $this->setOperationTermSignal(true);
    }

    /**
     * Steps Counter, sends term Signal, if limit reached
     *
     * @access private
     * @return void
     */
    private function stepCounter()
    {
        $this->increaseCounter();
        if (0 !== $this->operationLimit && $this->operationCounter >= $this->operationLimit)
        {
            $this->sendTermSignal();
        }
    }

}
