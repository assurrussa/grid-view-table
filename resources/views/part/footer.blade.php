@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
@endphp
<div class="row">
    <div class="col-sm-6 padding">
        <div class="pagination-fromToInfoFooter">
            @if(method_exists($data->data, 'total'))
                {{ \Assurrussa\GridView\GridView::trans('grid.gridCounts', [
                        'from'  => $data->data->firstItem(),
                        'to'    => $data->data->lastItem(),
                        'total' => $data->data->total(),
                ]) }}
            @elseif(property_exists($data->data, 'totalCount'))
                {{ \Assurrussa\GridView\GridView::trans('grid.gridCounts', [
                        'from'  => $data->data->firstItem(),
                        'to'    => $data->data->lastItem(),
                        'total' => $data->data->totalCount,
                ]) }}
            @else
                {{ \Assurrussa\GridView\GridView::trans('grid.gridCountsSimple', [
                        'from'  => $data->data->firstItem(),
                        'to'    => $data->data->lastItem(),
                ]) }}
            @endif
        </div>
    </div>
    <div class="col-sm-6">
        <ul class="js_filterSearchPagination pagination-sm no-padding no-margin pull-right float-right">
            {!! $data->pagination !!}
        </ul>
    </div>
</div>