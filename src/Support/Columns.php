<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\Interfaces\ColumnInterface;
use Assurrussa\GridView\Interfaces\ColumnsInterface;

/**
 * Class Columns
 *
 * @package Assurrussa\GridView\Support
 */
class Columns implements ColumnsInterface
{
    /** @var Column[] */
    private $_columns = [];
    /** @var  array */
    private $_fields = [];

    /**
     * Добавление необходимых полей для Grid
     *
     * @param \Closure|ColumnInterface $columns
     * @return $this
     */
    public function setColumn($column)
    {
        $this->_columns[] = $column;
        return $this;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->_fields = $fields;
        return $this;
    }

    /**
     * Получение необходимых полей для Grid
     *
     * @return Column[]
     */
    public function getColumns()
    {
        return $this->_columns;
    }

    /**
     * Получение необходимых полей для Grid
     *
     * @return Button[]|array
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
     * Метод получает кнопки для грид таблицы
     *
     * @param \Assurrussa\GridView\Models\Model|static $instance
     * return array
     */
    public function filterActions($instance)
    {
        $listButtons = [];
        if($this->count()) {
            $buttons = $this->getActions();
            foreach($buttons as &$button) {
                if($button->getValues($instance)) {
                    $listButtons[] = $button->render();
                    unset($button);
                }
            }
        }
        return $listButtons;
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
     * @return array
     */
    public function toFields()
    {
        if(count($this->_fields)) {
            return $this->_fields;
        }
        $columns = [];
        $pregMatch = config('amigrid.pregMatchForFields', "/[<]+/i");
        foreach($this->_columns as $column) {
            $key = $column->getValue();
            if(preg_match($pregMatch, $key)) {
                continue;
            }
            $value = ($column->isHandler()) ? $column->getHandler() : $column->getKey();

            $columns[$key] = $value;
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