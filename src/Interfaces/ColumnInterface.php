<?php

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface GridColumnInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface ColumnInterface
{

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param null $instance
     * @return mixed
     */
    public function getValues($instance = null);
}