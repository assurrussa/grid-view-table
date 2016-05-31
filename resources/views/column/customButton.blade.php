<?php
/**
 * @var string $url
 * @var string $title
 * @var string $class
 * @var string $icon
 * @var array  $options
 */
$url = isset($url) ? $url : '';
$title = isset($title) ? $title : '';
$class = isset($class) ? $class : '';
$icon = isset($icon) ? $icon : '';
$attributes = [];
if(isset($options)) {
    foreach($options as $attribute => $value) {
        $attributes[] = $attribute . '="' . e($value) . '"';
    }
}
?>
@if (!empty($url))
    <a href="" class="<?= e($class); ?>" data-href="<?= e($url); ?>" data-toggle="tooltip"
       title="<?= e($title); ?>" <?= implode(' ', $attributes); ?>>
        <?= e($icon); ?><?= e($title); ?>
    </a>
@endif