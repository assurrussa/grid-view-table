<?php

namespace Assurrussa\GridView\Support;

use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Class Pagination
 *
 * @package Assurrussa\GridView\Support
 */
class Pagination
{
    /**
     * @var LengthAwarePaginator
     */
    private $_data;

    /**
     * @param LengthAwarePaginator $data
     * @return $this
     */
    public function setData(LengthAwarePaginator $data)
    {
        $this->_data = $data;
        return $this;
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

    /**
     * @return array
     */
    public function toArray()
    {
        $list = [];
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
}