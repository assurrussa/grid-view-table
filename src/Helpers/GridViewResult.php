<?php

namespace Assurrussa\GridView\Helpers;

use Assurrussa\GridView\Support\Button;
use Assurrussa\GridView\Support\Column;
use Assurrussa\GridView\Support\Input;
use Illuminate\Pagination\LengthAwarePaginator;


/**
 * @property string                                      $id
 * @property \Illuminate\Pagination\LengthAwarePaginator $data
 * @property array                                       $pagination
 * @property Column[]                                    $headers
 * @property string|array|Button                         $buttonCreate
 * @property string|array|Button                         $buttonExport
 * @property array|Button[]                              $buttonCustoms
 * @property array|Input[]                               $inputCustoms
 * @property array                                       $filter
 * @property int                                         $page
 * @property string                                      $orderBy
 * @property string                                      $search
 * @property int                                         $limit
 * @property string                                      $sortName
 * @property array                                       $counts
 *
 * Class GridViewResult
 */
class GridViewResult
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = (array)$this;
        foreach($array as $key => $items) {
            if($items instanceof LengthAwarePaginator) {
                $array[$key] = $items->toArray();
            } elseif($items instanceof \stdClass) {
                $items = (array)$items;
                foreach($items as $keyItem => $valueItem) {
                    $items[$keyItem] = (array)$valueItem;
                }
                $array[$key] = $items;
            }
        }
        return $array;
    }

    /**
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * @param null $text
     * @return string
     */
    public function getElementName($text = null)
    {
        if($text) {
            $text = str_replace('.', '_', $text);
            return '_' . $text;
        }
        return 'js-amiGridList_' . $this->id;
    }

    /**
     * @param $property
     * @param $value
     */
    public function __set($property, $value)
    {
        if(is_array($value)) {
            foreach($value as $keyItem => $valueItem) {
                if(is_array($valueItem)) {
                    $value[$keyItem] = (object)$valueItem;
                } else {
                    $value[$keyItem] = $valueItem;
                }
            }
            $value = (object)$value;
        }
        $this->$property = $value;
    }

}