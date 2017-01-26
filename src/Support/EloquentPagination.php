<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\Interfaces\ColumnsInterface;
use Assurrussa\GridView\Interfaces\PaginationInterface;
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

    /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|static */
    private $_query;

    /** @var  ColumnsInterface */
    private $_columns;

    /**
     * @param $query
     * @return $this
     */
    public function setQuery(&$query)
    {
        $this->_query = $query;
        return $this;
    }

    /**
     * @param \Assurrussa\GridView\Support\Columns $columns
     * @return $this
     */
    public function setColumns(&$columns)
    {
        $this->_columns = $columns;
        return $this;
    }

    /**
     * Returns a collection in view of pagination
     * Возвращает коллекцию в виде пагинации
     *
     * @param int $page
     * @param int $limit
     * @return LengthAwarePaginator
     */
    public function get($page, $limit = 10)
    {
        if(!$this->_columns && !$this->_query) {
            return null;
        }
        $countTotal = $this->_query->count();
        $this->_query->skip(($limit * $page) - $limit);
        $this->_query->limit($limit);
        $data = collect();
        foreach($this->_query->get() as $key => $instance) {
            $_listRow = [];
            foreach($this->_columns->getColumns() as $column) {
                $_listRow[$column->getKey()] = $column->getValues($instance);
            }
            $buttons = $this->_columns->filterActions($instance);
            if(count($buttons)) {
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
     * @param string $view
     * @return string
     */
    public function render($view = null)
    {
        if(!$this->_data) {
            return '';
        }
        return $this->_data->appends(request()->all())->render($view);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $list = [];
        if(!$this->_data) {
            return $list;
        }
        if($this->_data->lastPage() > 1) {
            $window = \Illuminate\Pagination\UrlWindow::make($this->_data);

            $elements = [
                $window['first'],
                is_array($window['slider']) ? '...' : null,
                $window['slider'],
                is_array($window['last']) ? '...' : null,
                $window['last'],
            ];

            if($url = $this->_data->previousPageUrl() ?: '') {
                $list[] = $this->getItemForPagination('', '&laquo;', $url, 'prev', $this->_data->currentPage() - 1);
            };
            foreach($elements as $item) {
                if(is_string($item)) {
                    $list[] = $this->getItemForPagination('disabled', $item);
                }
                if(is_array($item)) {
                    foreach($item as $page => $url) {
                        if($page == $this->_data->currentPage()) {
                            $list[] = $this->getItemForPagination('active', $page);
                        } else {
                            $list[] = $this->getItemForPagination('', '', $url, '', $page);
                        }
                    }
                }
            }
            if($this->_data->hasMorePages()) {
                $list[] = $this->getItemForPagination('', '&raquo;', $this->_data->nextPageUrl(), 'next', $this->_data->currentPage() + 1);
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
     * @return array
     */
    public function getItemForPagination($status = '', $text = '', $url = '', $rel = '', $page = '')
    {
        return [
            'status' => $status,
            'text'   => $text,
            'url'    => $url,
            'rel'    => $rel,
            'page'   => $page,
        ];
    }
}