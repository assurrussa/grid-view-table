@php
/**
 * @var string $link
 * @var string $title
 * @var string $class
 * @var string $style
 */
$link = isset($link) ? $link : '';
$title = isset($title) ? $title : '';
$class = isset($class) ? $class : 'thumbnail';
$style = isset($style) ? $style : 'width: 80px;';
@endphp
<a href="{{ $link }}" data-lightbox="{{ $title }}" data-title="{{ $title }}" class="{{ $class }}">
    <img title="{{ $title }}" src="{{ $link }}" style="{{ $style }}">
</a>