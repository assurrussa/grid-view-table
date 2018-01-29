<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface GridColumnInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface ColumnInterface
{
    /**
     * Value for the field
     *
     * @param $value
     *
     * @return ColumnInterface
     */
    public function setValue($value): ColumnInterface;

    public function getValue();

    public function getHandler();

    public function getValues(\Illuminate\Database\Eloquent\Model $instance = null);

    public function getValueColumn(\Illuminate\Database\Eloquent\Model $instance, string $name);

    public function getActions();

    /**
     * Which field from the table to pull out
     *
     * @param string $key
     *
     * @return $this
     */
    public function setKey(string $key): ColumnInterface;

    /**
     * Method for a single column with actions
     *
     * @return ColumnInterface
     */
    public function setKeyAction(): ColumnInterface;

    public function setSort(bool $sort = false): ColumnInterface;

    public function setHandler(Callable $handler): ColumnInterface;

    public function setFilter(string $field, $array, string $mode = null, string $class = '', string $style = ''): ColumnInterface;

    public function setFilterSelect(string $field, array $array, string $class = '', string $style = ''): ColumnInterface;

    public function setFilterString(string $field, string $string = '', string $class = '', string $style = ''): ColumnInterface;

    public function setFilterDate(string $field, string $string = '', bool $active = true, string $format = null, string $class = '', string $style = ''): ColumnInterface;

    public function setDateFormat(string $format): ColumnInterface;

    public function setDateActive(bool $bool = false): ColumnInterface;

    public function setActions(Callable $action): ColumnInterface;

    public function setInstance(\Illuminate\Database\Eloquent\Model $instance): ColumnInterface;

    /**
     * Custom add string in form. Displaying Unescaped Data.
     * Be very careful when echoing content that is supplied by users of your application.
     * Always use the escaped, double curly brace syntax to prevent XSS attacks when displaying user supplied data.
     *
     * @param bool $screening
     *
     * @return $this
     */
    public function setScreening(bool $screening = false): ColumnInterface;

    /**
     * If you need your own handler for each row in the table.
     * Exanple:
     *   ->setClassForString(function ($data) {})
     *
     * @param callable $handler
     *
     * @return ColumnInterface
     */
    public function setClassForString(Callable $handler): ColumnInterface;

    public function isKeyAction(): bool;

    public function isSort(): bool;

    public function isScreening(): bool;

    public function isHandler(): bool;

    public function getKey(): string;

    public function getDateFormat(): string;

    public function getDateActive(): bool;

    public function getFilter(): array;

    public function getInstance(): ?\Illuminate\Database\Eloquent\Model;

    public function toArray(): array;
}