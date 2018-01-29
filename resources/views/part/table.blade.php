@php
/**
 * @var \Assurrussa\GridView\Helpers\GridViewResult $data
 */
$sortName = $data->sortName;
$orderBy = $data->orderBy;
@endphp
<div class="content-box">
    <input type="hidden" id="js-amiOrderBy" name="by" value="{{ $orderBy }}">
    <input type="hidden" id="js-amiSortName" name="sort" value="{{ $sortName }}">
    <table class="table table-stripped table-bordered table-hover table-condensed">
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
                <th id="js-amiTableHeader{{ $data->getElementName($header->key) }}"
                    class="{{ $sortName === $header->key ? 'active' : '' }} js-amiTableHeader"
                    data-name="{{ $header->key }}">
                    @if($header->screening && ($header->key === 'checkbox'))
                        {!! $header->value !!}
                    @else
                        {{ $header->value }}
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
            <tr class="js-loaderBody {{ $classString }}">
                @foreach($data->headers as $header)
                    @continue($header->key === \Assurrussa\GridView\Support\Column::ACTION_STRING_TR)
                    <td>
                        @if($header->screening)
                            {!! $item[$header->key] !!}
                        @else
                            {{ $item[$header->key] }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>


</div>