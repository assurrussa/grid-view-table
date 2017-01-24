<?php
/**
 * @var int    $id
 * @var string $name
 * @var string $icon
 * @var string $class
 * @var string $title
 * @var string $originTitle
 */
$id = isset($id) ? $id : null;
$name = isset($name) ? $name : null;
$icon = isset($icon) ? $icon : 'fa fa-filter';
$class = isset($class) ? $class : 'btn btn-xs btn-default';
$title = isset($title) ? $title : '';
$originTitle = isset($originTitle) ? $originTitle : Assurrussa\GridView\GridView::trans('grid.showSimilar');
?>
<?php if($name) { ?>
<a href="?<?= e($name); ?>=<?= (int)$id; ?>" class="<?= e($name); ?>">
    <i class="<?= e($icon); ?>"
       data-toggle="tooltip"
       title="<?= e($title); ?>"
       data-original-title="<?= e($originTitle); ?>">
    </i>
</a>
<?php } ?>