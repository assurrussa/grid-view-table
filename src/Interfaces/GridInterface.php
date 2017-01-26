<?php

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface GridInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface GridInterface
{

    /**
     * Добавление нужного Builder`а модели
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|static $query
     */
    public function setQuery($query);

    /**
     * Получение необходимых данных для пагинации
     *
     * return mixed
     */
    public function get();

    /**
     * @param array  $data
     * @param string $path
     * @param array  $mergeData
     * @return mixed
     */
    public function render($data = [], $path = 'gridView', $mergeData = []);
}