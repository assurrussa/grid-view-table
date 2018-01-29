@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
@endphp
<tr>
    @foreach($data->headers as $header)
        @continue($header->key === \Assurrussa\GridView\Support\Column::ACTION_STRING_TR)
        <td>
            @if(count($header->filter))
                @if($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] === \Assurrussa\GridView\Support\Column::FILTER_TYPE_STRING)
                    <div class="js-textFilter input-group">
                        <input type="text"
                               name="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                               value="{{ request()->get($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME]) }}"
                               id="js-textFilter_{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                               class="form-control {{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_CLASS] }}"
                               style="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_STYLE] }}"
                               data-mode="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] }}">
                    </div>
                @elseif($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] === \Assurrussa\GridView\Support\Column::FILTER_TYPE_DATE)
                    <div class="js-textFilter input-group date">
                        <input type="text"
                               name="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                               value="{{ request()->get($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME]) }}"
                               id="js-textFilter_{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                               class="form-control {{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_CLASS] }}"
                               style="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_STYLE] }}"
                               data-mode="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] }}">
                        <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                    </div>
                @else
                    @php
                        $filterValue = request()->get($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME], null);
                    @endphp
                    <select name="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                            id="js-selectFilter_{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                            class="js-selectFilter form-control {{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_CLASS] }}"
                            style="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_STYLE] }}"
                            data-mode="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] }}">
                        <option disabled>{{ \Assurrussa\GridView\GridView::trans('grid.selectFilter') }}</option>
                        <option value="" selected></option>
                        @foreach($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_DATA] as $key => $name)
                            <option value="{{ $key }}" {{ (($filterValue === $key) || ($filterValue === (string)$key)) ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                @endif
            @endif
        </td>
    @endforeach
</tr>
