<?php
/**
 * @var int    $id
 * @var string $name
 */
?>
@if (!empty($name))
    <a href="?<?= e($name); ?>=<?= (int)$id; ?>" class="btn btn-xs btn-default">
        <i class="fa fa-filter" data-toggle="tooltip" title=""
           data-original-title="<?= e(Assurrussa\GridView\GridView::trans('grid.showSimilar')); ?>"></i>
    </a>
@endif