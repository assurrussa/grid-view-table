<?php

namespace Assurrussa\GridView\Models;

/**
 * Class Model
 *
 * @package Assurrussa\GridView\Models
 */
class Model extends \Eloquent
{
    /**
     * Check if model's table has column
     *
     * @param string $column
     * @return bool
     */
    public function hasColumn($column)
    {
        $table = $this->getTable();
        $columns = \Cache::remember('amigridview.columns.' . $table, 60, function () use ($table) {
            return \Schema::getColumnListing($table);
        });
        return array_search($column, $columns) !== false;
    }
}