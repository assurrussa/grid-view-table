@if (!empty($url))
    <a href="{{ $url }}" class="btn btn-primary" data-toggle="tooltip" title="{{ trans(Assurrussa\GridView\GridView::NAME.'::grid.create') }}">
        <i class="fa fa-plus"></i> {{ trans(Assurrussa\GridView\GridView::NAME.'::grid.create') }}
    </a>
@endif