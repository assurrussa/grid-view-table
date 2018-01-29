@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
@endphp
<div class="row">
    <div class="col-sm-6">
        <label>
            <select id="js-amiSelectCount" name="count" class="input-sm">
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
        <div class="pull-right">
            <a id="js-filterSearchClearSubmit"
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
                           id="js-amiSearchInput"
                           name="search"
                           value="{{ request()->get('search') }}"
                           class="form-control input-sm"
                           placeholder="search">
                </label>
            @endif
        </div>
    </div>
</div>