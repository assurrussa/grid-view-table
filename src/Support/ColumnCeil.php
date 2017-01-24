<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\GridView;
use Assurrussa\GridView\Interfaces\ButtonInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\View;

/**
 * Class ColumnCeilAction
 *
 * @package Assurrussa\GridView\Support
 */
class ColumnCeil
{
    /**
     * @param bool   $bool
     * @param string $checked
     * @param string $unchecked
     * @return string
     */
    public function checked(bool $bool, $checked = '&check;', $unchecked = '-')
    {
        return $bool ? $checked : $unchecked;
    }

    /**
     * @param string $link
     * @param null   $title
     * @param null   $view
     * @return string
     */
    public function image($link, $title = null, $view = null)
    {
        $view = $view ?: 'column.linkImage';
        return GridView::view($view, [
            'link'  => $link,
            'title' => $title,
        ])->render();
    }

    /**
     * @param \Illuminate\Support\Collection|array|object $data
     * @param string                                      $title
     * @param string                                      $titleAddition
     * @param string                                      $class
     * @param string                                      $delimiter
     * @param string                                      $delimiterAddition
     * @param null                                        $view
     * @return string
     */
    public function listToString(
        $data,
        $title = null,
        $titleAddition = null,
        $class = null,
        $delimiter = null,
        $delimiterAddition = null,
        $view = null
    ) {
        $view = $view ?: 'column.listToString';
        return GridView::view($view, [
            'data'              => $data,
            'title'             => $title,
            'titleAddition'     => $titleAddition,
            'class'             => $class,
            'delimiter'         => $delimiter,
            'delimiterAddition' => $delimiterAddition,
        ])->render();
    }

    /**
     * @param int    $id
     * @param string $name
     * @param string $class
     * @param string $icon
     * @param string $title
     * @param string $originTitle
     * @param null   $view
     * @return string
     */
    public function filterButton($id, $name, $class = null, $icon = null, $title = null, $originTitle = null, $view = null)
    {
        $icon = $icon ? $icon : 'fa fa-filter';
        $class = $class ? $class : 'btn btn-xs btn-default';
        $title = $title ? $title : '';
        $originTitle = $originTitle ? $originTitle : GridView::trans('grid.showSimilar');
        $view = $view ?: 'column.listToString';
        return GridView::view($view, [
            'id'          => $id,
            'name'        => $name,
            'class'       => $class,
            'icon'        => $icon,
            'title'       => $title,
            'originTitle' => $originTitle,
        ])->render();
    }

    /**
     * Editable ceil button
     * * ===========================================
     * * string           -  $initJsEvent    - Init js event
     * * string           -  $initJsClass    - Init js class
     * * bool             -  $escape         -
     * * string           -  $class          -
     * * string           -  $text           -
     * * string|array|int -  $name           -
     * * string|array|int -  $value          -
     * * string           -  $url            -
     * * string           -  $type           - Type: text|textarea|select|checklist
     * * int              -  $pk             -
     * * array            -  $source         - Source data for list.
     * * string           -  $sourceCache    - If true and source is string url - results will be cached for fields with the same source. Usefull for
     * editable column in grid to prevent extra requests.
     * * string           -  $emptyText      -
     * * string           -  $originalTitle  - Original title
     * * string           -  $title          -
     * * int              -  $rows           -
     * * callable         -  $callback       - function
     * * ===========================================
     *
     * @param array $options
     * @param null  $view
     * @return string
     */
    public function editable($options = [], $view = null)
    {
        $view = $view ?: 'column.editableClick';
        $initJsEvent = isset($options['initJsEvent']) ? $options['initJsEvent'] : 'ready change';
        $initJsClass = isset($options['initJsClass']) ? $options['initJsClass'] : 'js-editableClick';
        $escape = isset($options['escape']) ? $options['escape'] : true;
        $class = isset($options['class']) ? $options['class'] : 'inline-editable editable editable-click editable-empty';
        $pk = isset($options['pk']) ? $options['pk'] : 1;
        $name = isset($options['name']) ? $options['name'] : 'name';
        $value = isset($options['value']) ? $options['value'] : null;
        $type = isset($options['type']) ? $options['type'] : 'text';
        $text = isset($options['text']) ? $options['text'] : null;
        $url = isset($options['url']) ? $options['url'] : '';
        $source = isset($options['source']) ? $options['source'] : null;
        $sourceCache = isset($options['sourceCache']) ? $options['sourceCache'] : null;
        $emptyText = isset($options['emptyText']) ? $options['emptyText'] : 'Empty';
        $originalTitle = isset($options['originalTitle']) ? $options['originalTitle'] : null;
        $title = isset($options['title']) ? $options['title'] : null;
        $rows = isset($options['rows']) ? $options['rows'] : 10;
        $callback = isset($options['callback']) ? $options['callback'] : null;
        return GridView::view($view, [
            'class'         => $class,
            'name'          => $name,
            'value'         => $value,
            'text'          => $text,
            'emptyText'     => $emptyText,
            'title'         => $title,
            'originalTitle' => $originalTitle,
            'type'          => $type,
            'url'           => $url,
            'pk'            => $pk,
            'escape'        => $escape,
            'initJsEvent'   => $initJsEvent,
            'initJsClass'   => $initJsClass,
            'source'        => $source,
            'sourceCache'   => $sourceCache,
            'rows'          => $rows,
            'callback'      => $callback,
        ])->render();
    }
}