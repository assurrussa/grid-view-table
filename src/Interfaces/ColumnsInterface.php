<?php

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface ColumnsInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface ColumnsInterface
{
    /**
     * @param $column
     * @return mixed
     */
    public function setColumn($column);

    /**
     * @return mixed
     */
    public function getColumns();

    /**
     * @return mixed
     */
    public function getActions();

    /**
     * @return int
     */
    public function count();

    /**
     * @return array
     */
    public function toArray();
}