<?php

declare(strict_types=1);

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
     *
     * @return string
     */
    public function checked(bool $bool, string $checked = '&check;', string $unchecked = '-'): string
    {
        return $bool ? $checked : $unchecked;
    }

    /**
     * @param string      $link
     * @param string|null $title
     * @param string|null $view
     *
     * @return string
     * @throws \Throwable
     */
    public function image(string $link, string $title = null, string $view = null): string
    {
        $view = $view ?: 'column.linkImage';

        return GridView::view($view, [
            'link'  => $link,
            'title' => $title,
        ])->render();
    }

    /**
     * @param             $data
     * @param string|null $title
     * @param string|null $titleAddition
     * @param string|null $class
     * @param string|null $delimiter
     * @param string|null $delimiterAddition
     * @param string|null $view
     *
     * @return string
     * @throws \Throwable
     */
    public function listToString(
        $data,
        string $title = null,
        string $titleAddition = null,
        string $class = null,
        string $delimiter = null,
        string $delimiterAddition = null,
        string $view = null
    ): string {
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
     * @param string      $name
     * @param string|null $id
     * @param string|null $class
     * @param string|null $icon
     * @param string|null $title
     * @param string|null $originTitle
     * @param string|null $view
     *
     * @return string
     * @throws \Throwable
     */
    public function filterButton(
        string $name,
        string $id,
        string $class = null,
        string $icon = null,
        string $title = null,
        string $originTitle = null,
        string $view = null
    ): string {
        $icon = $icon ? $icon : 'fa fa-filter';
        $class = $class ? $class : 'btn btn-xs btn-default';
        $title = $title ? $title : '';
        $originTitle = $originTitle ? $originTitle : GridView::trans('grid.showSimilar');
        $view = $view ?: 'column.filterButton';

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
     * * string           -  $sourceCache    - If true and source is string url - results will be cached for fields with the same source.
     * Usefull for editable column in grid to prevent extra requests.
     * * string           -  $emptyText      -
     * * string           -  $originalTitle  - Original title
     * * string           -  $title          -
     * * int              -  $rows           -
     * * callable         -  $callback       - function
     * * ===========================================
     *
     * @param array       $options
     * @param string|null $view
     *
     * @return string
     */
    public function editable(array $options = [], string $view = null): string
    {
        $view = $view ?: 'column.editableClick';
        $initJsEvent = isset($options['initJsEvent']) ? $options['initJsEvent'] : 'ready change';
        $initJsClass = isset($options['initJsClass']) ? $options['initJsClass'] : 'js_editableClick';
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