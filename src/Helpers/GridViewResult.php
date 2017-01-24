<?php

namespace Assurrussa\GridView\Helpers;

use Assurrussa\GridView\Support\Button;


/**
 * @property string                                                $id
 * @property \Illuminate\Contracts\Pagination\LengthAwarePaginator $data
 * @property array                                                 $pagination
 * @property array                                                 $headers
 * @property string|array|Button                                   $createButton
 * @property string|array|Button                                   $exportButton
 * @property string|array|Button                                   $customButton
 * @property array                                                 $filter
 * @property int                                                   $page
 * @property string                                                $orderBy
 * @property string                                                $search
 * @property int                                                   $count
 * @property string                                                $sortName
 *
 * Class GridViewResult
 */
class GridViewResult
{
    public $id;
    public $data;
    public $pagination;
    public $headers;
    public $createButton;
    public $exportButton;
    public $customButton;
    public $filter;
    public $page;
    public $orderBy;
    public $search;
    public $count;
    public $sortName;

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        return (array)$this;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }
}