@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
    $sortName = $data->sortName;
    $orderBy = $data->orderBy;
    $location = $data->location;
@endphp
<div class="content-box">
    <input type="hidden" id="js_amiOrderBy" name="by" value="{{ $orderBy }}">
    <input type="hidden" id="js_amiSortName" name="sort" value="{{ $sortName }}">
    <input type="hidden" id="js_amiLocation" name="location" value="{{ $location }}">
    <table class="table table-stripped table-condensed table-bordered table-hover">
        <thead>
        <tr>
            @foreach($data->headers as $header)
                @continue($header->key === \Assurrussa\GridView\Support\Column::ACTION_STRING_TR)
                @php
                    if ($sortName == $header->key) {
                        $orderByResult = $header->sort ? 'arrow ' . $orderBy : '';
                    } else {
                        $orderByResult = $header->sort ? 'arrow ' . \Assurrussa\GridView\Support\Column::FILTER_ORDER_BY_ASC : '';
                    }
                @endphp
                <th id="js_amiTableHeader{{ $data->getElementName($header->key) }}"
                    class="{{ $sortName === $header->key ? 'active' : '' }} js_amiTableHeader"
                    data-name="{{ $header->key }}">
                    @if($header->screening && ($header->key === 'checkbox'))
                        {!! $header->value !!}
                    @else
                        {{ e($header->value) }}
                    @endif
                    <span class="{{ $orderByResult }}"></span>
                </th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @include('amiGrid::part.tableFilter')
        @foreach($data->data->items() as $item)
            @php
                $classString = '';
                foreach ($data->headers as $header) {
                    if ($header->key === \Assurrussa\GridView\Support\Column::ACTION_STRING_TR) {
                        $classString = e($item[$header->key]);
                    }
                }
            @endphp
            <tr class="js_loaderBody {{ $classString }}">
                @foreach($data->headers as $header)
                    @continue($header->key === \Assurrussa\GridView\Support\Column::ACTION_STRING_TR)
                    <td>
                        @if($header->screening)
                            {!! $item[$header->key] !!}
                        @else
                            {{ e($item[$header->key]) }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>


</div>