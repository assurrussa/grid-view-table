<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Interfaces;


/**
 * Interface GridInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface GridInterface
{

    public static function view(string $view = null, array $data = [], array $mergeData = []): \Illuminate\Contracts\Support\Renderable;

    public static function trans(string $id = null, array $parameters = [], string $locale = null): string;

    public function setQuery(\Illuminate\Database\Eloquent\Builder $query): GridInterface;

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
    public function setFieldsForExport(array $array): GridInterface;

    public function button(): \Assurrussa\GridView\Support\Button;

    public function column(string $name = null, string $title = null): \Assurrussa\GridView\Support\Column;

    public function input(): \Assurrussa\GridView\Support\Input;

    public function columnActions(Callable $action, string $value = null): ColumnInterface;

    public function columnAction(): \Assurrussa\GridView\Support\Button;

    public static function columnCeil(): \Assurrussa\GridView\Support\ColumnCeil;

    public function get(): \Assurrussa\GridView\Helpers\GridViewResult;

    public function getSimple(bool $isCount = false): \Assurrussa\GridView\Helpers\GridViewResult;

    public function first(): \Assurrussa\GridView\Helpers\GridViewResult;

    public function setId(string $id): GridInterface;

    public function getId(): string;

    public function setCounts(array $array): GridInterface;

    public function setVisibleColumn(bool $visibleColumn): GridInterface;

    public function setStrictMode(bool $strictMode): GridInterface;

    public function setAjax(bool $isAjax): GridInterface;

    public function setTrimLastSlash(bool $isTrimLastSlash): GridInterface;

    public function setSearchInput(bool $searchInput = false): GridInterface;

    public function setOrderByDesc(): GridInterface;

    public function getOrderBy(): string;

    public function getSortName(): string;

    public function setSortName(string $sortNameDefault): GridInterface;

    public function setExport(bool $export): GridInterface;

    public function setFormAction(string $url): GridInterface;

    public function getFormAction(): string;

    public function isExport(): bool;

    public function isVisibleColumn(): bool;

    public function isSearchInput(): bool;

    public function isStrictMode(): bool;

    /**
     * @param array  $data
     * @param string $path
     * @param array  $mergeData
     *
     * @return mixed
     */
    public function render(array $data = [], string $path = 'gridView', array $mergeData = []): string;

    /**
     * @param array  $data
     * @param string $path
     * @param array  $mergeData
     *
     * @return mixed
     */
    public function renderFirst(array $data = [], string $path = 'gridView', array $mergeData = []): string;
}