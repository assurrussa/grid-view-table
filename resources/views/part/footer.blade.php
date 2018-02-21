@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
@endphp
<div class="row">
    <div class="col-sm-6 padding">
        <div class="pagination-fromToInfoFooter">
            {{ \Assurrussa\GridView\GridView::trans('grid.gridCounts', [
                    'from'  => $data->data->firstItem(),
                    'to'    => $data->data->lastItem(),
                    'total' => $data->data->total(),
            ]) }}
        </div>
    </div>
    <div class="col-sm-6">
        <ul class="js-filterSearchPagination pagination-sm no-padding no-margin pull-right">
            {!! $data->pagination !!}
        </ul>
    </div>
</div>