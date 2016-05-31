<?php
/**
 * @var string $url
 * @var string $class
 */
$class = isset($class) ? e($class) : 'btn-sm flat';
$url = isset($url) ? e($url) : '';
?>
@if ($action === 'show')
    <a href="<?= $url; ?>" class="btn btn-default <?= $class; ?>" data-toggle="tooltip"
       title="{{ trans(Assurrussa\GridView\GridView::NAME.'::grid.show') }}"><i class="fa fa-eye"></i></a>
@endif
@if ($action === 'edit')
    <a href="<?= $url; ?>" class="btn btn-default  <?= $class; ?>" data-toggle="tooltip"
       title="{{ trans(Assurrussa\GridView\GridView::NAME.'::grid.edit') }}"><i class="fa fa-pencil"></i></a>
@endif
@if ($action === 'delete')
    <form action="<?= $url; ?>" method="POST" style="display:inline-block;">
        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
        <input type="hidden" name="_method" value="DELETE"/>
        <button class="btn btn-danger <?= $class; ?> js-btnDelete"
                data-confirm-deleted="{{ trans(Assurrussa\GridView\GridView::NAME.'::grid.clickDelete') }}" data-toggle="tooltip"
                title="{{ trans(Assurrussa\GridView\GridView::NAME.'::grid.delete') }}">
            <i class="fa fa-times"></i>
        </button>
    </form>
@endif
@if ($action === 'restore')
    <form action="<?= $url; ?>" method="POST" style="display:inline-block;">
        <input type="hidden" name="_token" value="{{ csrf_token() }}"/>
        <button class="btn btn-primary <?= $class; ?>" data-toggle="tooltip"
                title="{{ trans(Assurrussa\GridView\GridView::NAME.'::grid.restore') }}">
            <i class="fa fa-reply"></i>
        </button>
    </form>
@endif