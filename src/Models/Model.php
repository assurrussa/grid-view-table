<?php

declare(strict_types=1);

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
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string                              $column
     *
     * @return bool
     */
    public static function hasColumn(\Illuminate\Database\Eloquent\Model $model, string $column): bool
    {
        $table = $model->getTable();
        $columns = app('cache')->remember('amigrid.columns.' . $table, 60, function () use ($table) {
            return \Schema::getColumnListing($table);
        });
        return array_search($column, $columns) !== false;
    }
}