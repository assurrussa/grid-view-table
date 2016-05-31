<?php
/**
 * @var string $link
 * @var string $title
 * @var string $class
 */
$class = isset($class) ? $class : '';
?>
<a href="{{ $link }}" data-lightbox="{{ $title }}" data-title="{{ $title }}">
    <img class="thumbnail {{ $class }}" title="{{ $title }}" src="{{ $link }}" width="80px">
</a>