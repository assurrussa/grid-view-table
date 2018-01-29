<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface ColumnsInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface ColumnsInterface
{
    public function setColumn(\Assurrussa\GridView\Interfaces\ColumnInterface $column): ColumnsInterface;

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
    public function setFields(array $fields): ColumnsInterface;

    public function getColumns(): array;

    public function getActions(): array;

    /**
     * The method gets the buttons for the grid table
     *
     * @param \Illuminate\Database\Eloquent\Model $instance
     *
     * @return array
     */
    public function filterActions(\Illuminate\Database\Eloquent\Model $instance): array;

    public function count(): int;

    public function toArray(): array;

    public function toFields(): array;
}