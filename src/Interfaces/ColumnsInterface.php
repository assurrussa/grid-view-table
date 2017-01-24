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
     * @return ColumnInterface[]
     */
    public function getColumns();

    /**
     * @return ButtonInterface[]
     */
    public function getActions();

    /**
     * @param $instance
     * @return ButtonInterface[]
     */
    public function filterActions($instance);

    /**
     * @return int
     */
    public function count();

    /**
     * @return array
     */
    public function toArray();
}