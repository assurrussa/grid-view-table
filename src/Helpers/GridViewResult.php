<?php

declare(strict_types=1);


namespace Assurrussa\GridView\Helpers;

use Assurrussa\GridView\Support\Button;
use Assurrussa\GridView\Support\Column;
use Assurrussa\GridView\Support\Input;
use Illuminate\Pagination\LengthAwarePaginator;


/**
 * @property string                                                                                 $id
 * @property boolean                                                                                $simple
 * @property string                                                                                 $location
 * @property string                                                                                 $formAction
 * @property string                                                                                 $requestParams
 * @property \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator $data
 * @property array                                                                                  $pagination
 * @property Column[]                                                                               $headers
 * @property string|array|Button                                                                    $buttonCreate
 * @property string|array|Button                                                                    $buttonExport
 * @property array|Button[]                                                                         $buttonCustoms
 * @property array|Input[]                                                                          $inputCustoms
 * @property array                                                                                  $filter
 * @property int                                                                                    $page
 * @property string                                                                                 $orderBy
 * @property string                                                                                 $search
 * @property int                                                                                    $limit
 * @property string                                                                                 $sortName
 * @property array                                                                                  $counts
 * @property bool                                                                                   $searchInput
 * @property int                                                                                    $milliSeconds
 *
 * Class GridViewResult
 */
class GridViewResult
{

    public $simple = false;

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = (array)$this;
        foreach ($array as $key => $items) {
            if ($items instanceof LengthAwarePaginator) {
                $array[$key] = $items->toArray();
            } elseif ($items instanceof \stdClass) {
                $items = (array)$items;
                foreach ($items as $keyItem => $valueItem) {
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
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @param string|null $text
     *
     * @return string
     */
    public function getElementName(string $text = null): string
    {
        if ($text) {
            $text = str_replace('.', '_', $text);

            return '_' . $text;
        }

        return 'js-amiGridList_' . $this->id;
    }

    /**
     * @param string $property
     * @param        $value
     */
    public function __set(string $property, $value): void
    {
        if (is_array($value)) {
            foreach ($value as $keyItem => $valueItem) {
                if (is_array($valueItem)) {
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