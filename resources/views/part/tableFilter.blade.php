@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
$gridId = $data->getElementName();
@endphp
<tr>
    @foreach($data->headers as $header)
        @continue($header->key === \Assurrussa\GridView\Support\Column::ACTION_STRING_TR)
        <td>
            @if($header->key === \Assurrussa\GridView\Support\Column::ACTION_NAME)
                <button type="submit" class="js_amiGridFilterTable btn btn-default btn-outline-primary btn-sm pull-right float-right">
                    {{ \Assurrussa\GridView\GridView::trans('grid.filter') }} <i class="fa fa-filter"></i>
                </button>
            @elseif(count($header->filter))
                @if($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] === \Assurrussa\GridView\Support\Column::FILTER_TYPE_STRING)
                    <div class="js_textFilter input-group">
                        <input type="text"
                               name="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                               value="{{ request()->get($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME]) }}"
                               id="js_textFilter_{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                               class="form-control {{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_CLASS] }}"
                               style="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_STYLE] }}"
                               placeholder="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_PLACEHOLDER] }}"
                               data-mode="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] }}">
                    </div>
                @elseif($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] === \Assurrussa\GridView\Support\Column::FILTER_TYPE_DATE)
                    @php
                        $placeholder = $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_PLACEHOLDER];
                        $placeholder = $placeholder ? $placeholder : '-- date --';
                    @endphp
                    <div class="js_InitComponent">
                        <ami-datepicker name="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                                        value="{{ request()->get($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME]) }}"
                                        class="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_CLASS] }}"
                                        grid-id="{{ $gridId }}"
                                        placeholder="{{ $placeholder }}"
                                        width="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_WIDTH] }}"
                                        format="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_FORMAT] }}"
                                        i18n="{{ config('app.locale') ?? 'EN' }}">
                        </ami-datepicker>
                    </div>
                @elseif($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] === \Assurrussa\GridView\Support\Column::FILTER_TYPE_DATE_RANGE)
                    @php
                        $placeholder = $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_PLACEHOLDER];
                        $placeholder = $placeholder ? $placeholder : '-- range --';
                    @endphp
                    <div class="js_InitComponent">
                        <ami-select-date-range name="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                                               value="{{ request()->get($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME]) }}"
                                               class="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_CLASS] }}"
                                               grid-id="{{ $gridId }}"
                                               create-date=""
                                               placeholder="{{ $placeholder }}"
                                               compact="true"
                                               width="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_WIDTH] }}"
                                               format="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_FORMAT] }}"
                                               i18n="{{ config('app.locale') ?? 'EN' }}">
                        </ami-select-date-range>
                    </div>
                @elseif($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] === \Assurrussa\GridView\Support\Column::FILTER_TYPE_SELECT_AJAX)
                    @php
                        $filterData = json_encode($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_DATA]);
                        $filterSelected = json_encode($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_SELECTED]);
                    @endphp
                    <div class="js_InitComponent">
                        <ami-select-ajax data='{{ $filterData }}'
                                         clear="1"
                                         search-ajax="1"
                                         placeholder="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_PLACEHOLDER] }}"
                                         js-init-submit="{{ $gridId }}"
                                         name="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                                         action="{{ $header->url }}"
                                         selected='{{ $filterSelected }}'></ami-select-ajax>
                    </div>
                @elseif($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] === \Assurrussa\GridView\Support\Column::FILTER_TYPE_SELECT_NOT_AJAX)
                    @php
                        $filterData = json_encode($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_DATA]);
                        $filterSelected = json_encode($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_SELECTED]);
                    @endphp
                    <div class="js_InitComponent">
                        <ami-select-ajax data='{{ $filterData }}'
                                         clear="1"
                                         placeholder="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_PLACEHOLDER] }}"
                                         js-init-submit="{{ $gridId }}"
                                         name="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                                         action="{{ $header->url }}"
                                         selected='{{ $filterSelected }}'></ami-select-ajax>
                    </div>
                @elseif($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_MODE] === \Assurrussa\GridView\Support\Column::FILTER_TYPE_SELECT)
                    @php
                        $filterValue = request()->get($header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME], null);
                    @endphp
                    <select name="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                            id="js_selectFilter_{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_NAME] }}"
                            class="js_selectFilter form-control {{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_CLASS] }}"
                            placeholder="{{ $header->filter[\Assurrussa\GridView\Support\Column::FILTER_KEY_PLACEHOLDER] }}"
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
