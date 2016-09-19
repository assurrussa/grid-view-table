<?php

namespace Assurrussa\GridView\Models;

/**
 * Class Model
 *
 * @package Assurrussa\GridView\Models
 */
class Model extends \Illuminate\Database\Eloquent\Model
{
    /**
     * Check if model's table has column
     *
     * @param \Eloquent $model
     * @param string $column
     * @return bool
     */
    public static function hasColumn($model, $column)
    {
        $table = $model->getTable();
        $columns = \Cache::remember('amigridview.columns.' . $table, 60, function () use ($table) {
            return \Schema::getColumnListing($table);
        });
        return array_search($column, $columns) !== false;
    }
}