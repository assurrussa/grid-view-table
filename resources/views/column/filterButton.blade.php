@php
    /**
     * @var int    $id
     * @var string $name
     * @var string $icon
     * @var string $class
     * @var string $title
     * @var string $originTitle
     */
    $id = isset($id) ? (int)$id : 0;
    $name = isset($name) ? $name : null;
    $icon = isset($icon) ? $icon : 'fa fa-filter';
    $class = isset($class) ? $class : 'btn btn-xs btn-default';
    $title = isset($title) ? $title : '';
    $originTitle = isset($originTitle) ? $originTitle : Assurrussa\GridView\GridView::trans('grid.showSimilar');
@endphp
@if($name)
    <a href="?{{ $name }}={{ $id }}" class="{{ $name }}">
        <i class="{{ $icon }}"
           data-toggle="tooltip"
           title="{{ $title }}"
           data-original-title="{{ $originTitle }}">
        </i>
    </a>
@endif