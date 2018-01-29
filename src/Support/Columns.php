<?php

declare(strict_types=1);

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
     * @param ColumnInterface $column
     *
     * @return ColumnInterface
     */
    public function setColumn(\Assurrussa\GridView\Interfaces\ColumnInterface $column): ColumnsInterface
    {
        $this->_columns[] = $column;

        return $this;
    }

    /**
     * example:
     * $fields = [
     *             'ID'            => 'id',
     *             'Time'          => 'setup_at',
     *             0               => 'brand.name',
     *             'Name'          => function() {return 'name';},
     *         ];
     *
     * @param array $fields
     *
     * @return $this
     */
    public function setFields(array $fields): ColumnsInterface
    {
        $this->_fields = $fields;

        return $this;
    }

    /**
     * @return array|\Assurrussa\GridView\Interfaces\ColumnInterface[]
     */
    public function getColumns(): array
    {
        return $this->_columns;
    }

    /**
     * @return array|\Assurrussa\GridView\Interfaces\ColumnInterface[]
     */
    public function getActions(): array
    {
        foreach ($this->getColumns() as $column) {
            if ($column->isKeyAction()) {
                return $column->getActions();
            }
        }

        return [];
    }

    /**
     * The method gets the buttons for the grid table
     *
     * @param \Illuminate\Database\Eloquent\Model $instance
     *
     * @return array
     */
    public function filterActions(\Illuminate\Database\Eloquent\Model $instance): array
    {
        $listButtons = [];
        if ($this->count()) {
            $buttons = $this->getActions();
            foreach ($buttons as &$button) {
                if ($button->getValues($instance)) {
                    $listButtons[] = $button->render();
                    unset($button);
                }
            }
        }

        return $listButtons;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $columns = [];
        foreach ($this->_columns as $column) {
            $columns[] = $column->toArray();
        }

        return $columns;
    }

    /**
     * @return array
     */
    public function toFields(): array
    {
        if (count($this->_fields)) {
            return $this->_fields;
        }
        $columns = [];
        $pregMatch = config('amigrid.pregMatchForFields', "/[<]+/i");
        foreach ($this->_columns as $column) {
            $key = $column->getValue();
            if (preg_match($pregMatch, $key)) {
                continue;
            }
            $value = ($column->isHandler()) ? $column->getHandler() : $column->getKey();

            $columns[$key] = $value;
        }

        return $columns;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->_columns);
    }
}