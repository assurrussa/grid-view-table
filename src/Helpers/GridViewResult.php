<?php

declare(strict_types=1);


namespace Assurrussa\GridView\Helpers;

use Assurrussa\GridView\Support\Button;
use Assurrussa\GridView\Support\Column;
use Assurrussa\GridView\Support\Input;


/**
 * @property string                                                                                       $id
 * @property boolean                                                                                      $ajax
 * @property boolean                                                                                      $isTrimLastSlash
 * @property boolean                                                                                      $simple
 * @property string                                                                                       $formAction
 * @property array                                                                                        $requestParams
 * @property \Illuminate\Pagination\LengthAwarePaginator|\Illuminate\Contracts\Pagination\Paginator|array $data
 * @property string                                                                                       $pagination
 * @property Column[]                                                                                     $headers
 * @property string|array|Button                                                                          $buttonCreate
 * @property string|array|Button                                                                          $buttonExport
 * @property array|Button[]                                                                               $buttonCustoms
 * @property array|Input[]                                                                                $inputCustoms
 * @property array                                                                                        $filter
 * @property int                                                                                          $page
 * @property string                                                                                       $orderBy
 * @property string                                                                                       $search
 * @property int                                                                                          $limit
 * @property string                                                                                       $sortName
 * @property array                                                                                        $counts
 * @property bool                                                                                         $searchInput
 * @property object|null                                                                                  $exportData
 *
 * Class GridViewResult
 */
class GridViewResult
{
    public $simple = false;

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

        return 'js_amiGridList_' . $this->id;
    }

    /**
     * @return bool
     */
    public function isAjax(): bool
    {

        return (bool)$this->ajax;
    }

    /**
     * @return bool
     */
    public function isTrimLastSlash(): bool
    {

        return (bool)$this->isTrimLastSlash;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = (array)$this;
        foreach ($array as $key => $items) {
            if ($items instanceof \Illuminate\Pagination\LengthAwarePaginator) {
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
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|null
     */
    public function getExport(): ?\Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        if ($this->exportData !== null) {
            return response()->download($this->exportData->path, $this->exportData->filename, []);
        }
        return null;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    /**
     * @return string
     */
    public function toHtml(): string
    {
        return app(\Assurrussa\GridView\GridView::NAME)->render(['data' => $this]);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toHtml();
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
