<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\Interfaces\ColumnsInterface;
use Assurrussa\GridView\Interfaces\PaginationInterface;
use Illuminate\Database\Query\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class Pagination
 *
 * @package Assurrussa\GridView\Support
 */
class EloquentPagination implements PaginationInterface
{
    /** @var LengthAwarePaginator */
    private $_data;

    /** @var \Illuminate\Database\Eloquent\Builder|static */
    private $_query;

    /** @var  ColumnsInterface */
    private $_columns;

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return PaginationInterface
     */
    public function setQuery(\Illuminate\Database\Eloquent\Builder &$query): PaginationInterface
    {
        $this->_query = $query;

        return $this;
    }

    /**
     * @param Columns $columns
     *
     * @return PaginationInterface
     */
    public function setColumns(Columns &$columns): PaginationInterface
    {
        $this->_columns = $columns;

        return $this;
    }

    /**
     * Returns a collection in view of pagination
     *
     * @param int $page
     * @param int $limit
     *
     * @return LengthAwarePaginator
     */
    public function get(int $page, int $limit = 10): \Illuminate\Pagination\LengthAwarePaginator
    {
        if (!$this->_columns && !$this->_query) {
            return null;
        }
        $countTotal = $this->_query->count();
        $this->_query->skip(($limit * $page) - $limit);
        $this->_query->limit($limit);
        $data = collect();
        foreach ($this->_query->get() as $key => $instance) {
            $_listRow = [];
            foreach ($this->_columns->getColumns() as $column) {
                $_listRow[$column->getKey()] = $column->getValues($instance);
            }
            $buttons = $this->_columns->filterActions($instance);
            if (count($buttons)) {
                $_listRow = array_merge($_listRow, [Column::ACTION_NAME => implode('', $buttons)]);
            }
            $data->offsetSet($key, $_listRow);
        }
        $this->_data = new \Illuminate\Pagination\LengthAwarePaginator($data, $countTotal, $limit, $page, [
            'path'     => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        return $this->_data;
    }

    /**
     * Returns a collection in view of pagination
     *
     * @param int $page
     * @param int $limit
     *
     * @return \Illuminate\Contracts\Pagination\Paginator
     */
    public function getSimple(int $page, int $limit = 10, bool $isClount = false): \Illuminate\Contracts\Pagination\Paginator
    {
        if (!$this->_columns && !$this->_query) {
            return null;
        }
        if($isClount) {
            $countTotal = $this->_query->count();
        }
        $this->_query->skip(($page - 1) * $page)->take($page + 1);
        $this->_data = $this->_query->simplePaginate($limit);
        if($isClount) {
            $this->_data->totalCount = $countTotal;
        }
        foreach ( $this->_data->items() as $key => $instance) {
            $_listRow = [];
            foreach ($this->_columns->getColumns() as $column) {
                $_listRow[$column->getKey()] = $column->getValues($instance);
                if($column->getKey() === 'preview') {
                }
            }
            $buttons = $this->_columns->filterActions($instance);
            if (count($buttons)) {
                $_listRow = array_merge($_listRow, [Column::ACTION_NAME => implode('', $buttons)]);
            }
            $this->_data->offsetSet($key, $_listRow);
        }

        return $this->_data;
    }

    /**
     * @param string|null $view
     * @param array       $data
     * @param string      $formAction
     *
     * @return string
     */
    public function render(string $view = null, array $data = [], string $formAction = ''): string
    {
        if (!$this->_data) {
            return '';
        }

        return $this->_data->setPath($formAction)->appends($data)->render($view)->toHtml();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $list = [];
        if (!$this->_data) {
            return $list;
        }
        if ($this->_data->lastPage() > 1) {
            $window = \Illuminate\Pagination\UrlWindow::make($this->_data);

            $elements = [
                $window['first'],
                is_array($window['slider']) ? '...' : null,
                $window['slider'],
                is_array($window['last']) ? '...' : null,
                $window['last'],
            ];

            if ($url = $this->_data->previousPageUrl() ?: '') {
                $list[] = $this->getItemForPagination('', '&laquo;', $url, 'prev', (string)($this->_data->currentPage() - 1));
            };
            foreach ($elements as $item) {
                if (is_string($item)) {
                    $list[] = $this->getItemForPagination('disabled', $item);
                }
                if (is_array($item)) {
                    foreach ($item as $page => $url) {
                        if ($page == $this->_data->currentPage()) {
                            $list[] = $this->getItemForPagination('active', $page);
                        } else {
                            $list[] = $this->getItemForPagination('', '', $url, '', $page);
                        }
                    }
                }
            }
            if ($this->_data->hasMorePages()) {
                $list[] = $this->getItemForPagination('', '&raquo;', $this->_data->nextPageUrl(), 'next', (string)($this->_data->currentPage() + 1));
            };
        }

        return $list;
    }

    /**
     * @param string $status
     * @param string $text
     * @param string $url
     * @param string $rel
     * @param string $page
     *
     * @return array
     */
    public function getItemForPagination(
        string $status = '',
        string $text = '',
        string $url = '',
        string $rel = '',
        string $page = ''
    ): array {
        return [
            'status' => $status,
            'text'   => $text,
            'url'    => $url,
            'rel'    => $rel,
            'page'   => $page,
        ];
    }
}