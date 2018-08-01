@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
@endphp
<div class="row">
    <div class="col-sm-6">
        <span class="pagination-fromToInfoHeader">
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
            @endif
        </span>
        <label>
            <select id="js_amiSelectCount" name="count" class="input-sm form-control">
                @foreach($data->counts as $key => $value)
                    <option {{ request()->get('count') == $key ? 'selected' : '' }} value="{{ $key }}">
                        {{ $value }}
                    </option>
                @endforeach
            </select>
        </label>
        {!! $data->buttonCreate  !!}
        {!! $data->buttonExport  !!}
    </div>
    <div class="col-sm-6">
        <div class="pull-right float-right">
            <a id="js_filterSearchClearSubmit"
               class="btn btn-default btn-sm"
               data-toggle="tooltip"
               data-original-title="{{ \Assurrussa\GridView\GridView::trans('grid.clearFilter') }}"
               href="">
                <i class="fa fa-times"></i>
                {{ \Assurrussa\GridView\GridView::trans('grid.clear') }}
            </a>
            @if($data->searchInput)
                <label>
                    <input type="text"
                           id="js_amiSearchInput"
                           name="search"
                           value="{{ request()->get('search') }}"
                           class="form-control input-sm"
                           placeholder="search">
                </label>
            @endif
        </div>
    </div>
</div>