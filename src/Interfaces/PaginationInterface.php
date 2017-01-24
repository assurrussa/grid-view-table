<?php

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface PaginationInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface PaginationInterface
{

    /**
     * @param $query
     * @return mixed
     */
    public function setQuery(&$query);

    /**
     * @param $columns
     * @return mixed
     */
    public function setColumns(&$columns);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return mixed
     */
    public function get($page, $limit);
}