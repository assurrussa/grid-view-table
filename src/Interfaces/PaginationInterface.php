<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface PaginationInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface PaginationInterface
{
    public function setQuery(\Illuminate\Database\Eloquent\Builder &$query): PaginationInterface;

    public function setColumns(\Assurrussa\GridView\Support\Columns &$columns): PaginationInterface;

    public function get(int $page, int $limit = 10): \Illuminate\Pagination\LengthAwarePaginator;

    public function render(string $view = null, array $data = [], string $formAction = ''): string;

    public function toArray(): array;

    public function getItemForPagination(
        string $status = '',
        string $text = '',
        string $url = '',
        string $rel = '',
        string $page = ''
    ): array;
}