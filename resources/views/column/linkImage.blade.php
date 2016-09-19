
<?php
/**
 * @var string $link
 * @var string $title
 * @var string $class
 */
$class = isset($class) ? $class : '';
?>
<a href="{{ $link }}" data-lightbox="{{ $title }}" data-title="{{ $title }}" class="thumbnail {{ $class }}">
    <img title="{{ $title }}" src="{{ $link }}" width="80px">
</a>