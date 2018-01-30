@php
    /**
     * @var array $item
     * @var \Assurrussa\GridView\Support\Column[]       $headers
     */
if(!count($item)) {
return null;
}
@endphp
@foreach($headers as $header)
    @continue($header->key === \Assurrussa\GridView\Support\Column::ACTION_STRING_TR)
    <td>
        @if($header->screening)
            {!! $item[$header->key] !!}
        @else
            {{ e($item[$header->key]) }}
        @endif
    </td>
@endforeach