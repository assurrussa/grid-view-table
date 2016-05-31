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
    public function query($query);

    /**
     * Добавление необходимых полей для Grid
     *
     * @param \Closure $callback
     * @return $this
     */
    public function columns($columns);

    /**
     * Получение необходимых данных для пагинации
     *
     * return mixed
     */
    public function get();
}