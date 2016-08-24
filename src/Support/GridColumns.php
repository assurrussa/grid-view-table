<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\Interfaces\GridColumnInterface;
use Assurrussa\GridView\Interfaces\GridColumnsInterface;

/**
 * Class GridColumns
 *
 * @package Assurrussa\AmiCMS\Components\Support
 */
class GridColumns implements GridColumnsInterface
{

    /** @var GridColumn[] */
    private $_columns = [];

    /**
     * GridColumns constructor.
     * @param null|GridColumnInterface $columns
     */
    public function __construct($columns = null)
    {
        if($columns) {
            $this->setColumns($columns);
        }
    }

    /**
     * Создает класс GridColumn
     *
     * @return GridColumn
     */
    public function column()
    {
        return new GridColumn();
    }

    /**
     * Создает класс ButtonItem
     *
     * @return ButtonItem
     */
    public function button()
    {
        return new ButtonItem();
    }

    /**
     * Добавление необходимых полей для Grid
     *
     * @param \Closure|GridColumnInterface[] $columns
     * @return $this
     */
    public function setColumns($columns)
    {
        if(is_callable($columns)) {
            $columns = call_user_func($columns, $this);
        }
        $this->_columns = $columns;
        return $this;
    }

    /**
     * Получение необходимых полей для Grid
     *
     * @return GridColumn[]
     */
    public function getColumns()
    {
        return $this->_columns;
    }

    /**
     * Получение необходимых полей для Grid
     *
     * @return ButtonItem[]|[]
     */
    public function getActions()
    {
        foreach($this->getColumns() as $column) {
            if($column->isKeyAction()) {
                return $column->getActions();
            }
        }
        return [];
    }

    /**
     * Получение необходимых полей для Grid
     *
     * @return array
     */
    public function toArray()
    {
        $columns = [];
        foreach($this->_columns as $column) {
            $columns[] = $column->toArray();
        }
        return $columns;
    }

    /**
     * Получение количества колонок
     *
     * @return int
     */
    public function count()
    {
        return count($this->_columns);
    }
}