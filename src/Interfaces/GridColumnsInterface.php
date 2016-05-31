<?php

namespace Assurrussa\GridView\Interfaces;

use Assurrussa\GridView\Support\ButtonItem;
use Assurrussa\GridView\Support\GridColumn;

/**
 * Interface GridColumnsInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface GridColumnsInterface
{

    /**
     * Добавление необходимых колонок для Grid
     *
     * @param GridColumnInterface|GridColumn[] $columns
     * @return mixed
     */
    public function setColumns($columns);

    /**
     * Получение необходимых полей для Grid
     *
     * @return GridColumnInterface|GridColumn[]
     */
    public function getColumns();

    /**
     * Создает класс GridColumn
     *
     * @return GridColumn
     */
    public function column();

    /**
     * Создает класс ButtonItem
     *
     * @return ButtonItem
     */
    public function button();

    /**
     * Получение количества колонок
     *
     * @return int
     */
    public function count();
}