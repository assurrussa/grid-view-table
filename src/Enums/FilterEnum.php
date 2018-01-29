<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Enums;

/**
 * Class FilterEnum
 *
 * @package Assurrussa\GridView\Enums
 */
class FilterEnum
{
    /**
     * Костылёк.
     * Какие поля нужно исключить в случаи кирилического поиска данных.
     *
     * @var array
     */
    public static $filterExecuteForCyrillicColumn = [
        '_at',
    ];

    /**
     * Получение полей исключения в случаи кирилицы
     *
     * @return array
     */
    public static function getFilterExecuteForCyrillicColumn()
    {
        return static::$filterExecuteForCyrillicColumn;
    }

    /**
     * Проверка существует ли имя в массиве
     *
     * @param string $column
     * @return bool
     */
    public static function hasFilterExecuteForCyrillicColumn($column)
    {
        return in_array(mb_substr($column, -3), static::getFilterExecuteForCyrillicColumn());
    }
}