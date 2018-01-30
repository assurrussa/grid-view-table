@php
    /**
     * @var string $id
     * @var string $class
     * @var string $jsClass
     * @var string $action
     * @var string $label
     * @var string $icon
     * @var string $url
     * @var string $method
     * @var string $confirmText
     * @var string $confirmTextOk
     * @var string $confirmTextCancel
     * @var string $confirmColorOk
     * @var string $confirmColorCancel
     * @var string $type
     * @var array  $options
     * @var array  $strings
     */
    use \Assurrussa\GridView\Support\Button;

    $type = !empty($type) ? $type : null;
    $method = !empty($method) ? $method : 'POST';
    $confirmText = !empty($confirmText) ? $confirmText : \Assurrussa\GridView\GridView::trans('grid.clickDelete');
    $confirmTextOk = !empty($confirmTextOk) ? $confirmTextOk : 'ok';
    $confirmTextCancel = !empty($confirmTextCancel) ? $confirmTextCancel : 'ok';
    $confirmColorOk = !empty($confirmColorOk) ? $confirmColorOk : 'btn-primary';
    $confirmColorCancel = !empty($confirmColorCancel) ? $confirmColorCancel : 'btn-default';
    $id = !empty($id) ? $id : '';
    $class = !empty($class) ? $class : 'btn btn-default btn-sm flat';
    $jsClass = !empty($jsClass) ? $jsClass : '';
    $action = !empty($action) ? $action : '';
    $label = !empty($label) ? $label : '';
    $title = !empty($title) ? $title : '';
    $icon = !empty($icon) ? $icon : null;
    $url = !empty($url) ? $url : '';
    $strings = !empty($strings) ? $strings : [];
    $attributes = [];
    if (isset($options)) {
        foreach ($options as $attribute => $value) {
            $attributes[] = $attribute . '="' . e($value) . '"';
        }
    }
@endphp
@if($type === Button::TYPE_LINK)
    <a href="{{ $url }}"
       class="{{ $jsClass }} {{ $class }}"
       id="{{ $id }}"
       data-confirm="{{ $confirmText }}"
       data-btn-ok-text="{{ $confirmTextOk }}"
       data-btn-ok-color="{{ $confirmColorOk }}"
       data-btn-cancel-text="{{ $confirmTextCancel }}"
       data-btn-cancel-color="{{ $confirmColorCancel }}"
       data-toggle="tooltip"
       title="{{ $title }}" {{ implode(' ', $attributes) }}>
        @if($icon)
            <i class="{{ $icon }}"></i>
        @endif
        {{ $label }}
    </a>
@elseif($action === \Assurrussa\GridView\Support\Button::TYPE_ACTION_DELETE)
    <form action="{{ $url }}" method="POST" style="display:inline-block;">
        <input type="hidden" name="_method" value="DELETE"/>
        {{ csrf_field() }}
        @foreach ($strings as $string)
            {!! $string !!}
        @endforeach
        <button id="{{ $id }}"
                class="{{ !empty($jsClass) ? $jsClass : 'js-btnDelete' }} {{ $class }}"
                data-confirm="{{ $confirmText }}"
                data-btn-ok-text="{{ $confirmTextOk }}"
                data-btn-ok-color="{{ $confirmColorOk }}"
                data-btn-cancel-text="{{ $confirmTextCancel }}"
                data-btn-cancel-color="{{ $confirmColorCancel }}"
                data-toggle="tooltip"
                title="{{ $title }}">
            @if($icon)
                <i class="{{ $icon }}"></i>
            @endif
            {{ $label }}
        </button>
    </form>
@else
    @php
        $addString = false;
        if (($method !== 'POST') && ($method !== 'GET')) {
        $addString = true;
        $addMethod = $method;
        $method = 'POST';
        }
    @endphp
    <form action="{{ $url }}" method="{{ $method }}" style="display:inline-block;">
        @if($addString)
            <input type="hidden" name="_method" value="{{ $addMethod }}">
        @endif
        {{ csrf_field() }}
        @foreach ($strings as $string)
            {!! $string !!}
        @endforeach
        <button id="{{ $id }}"
                class="{{ $jsClass }} {{ $class }}"
                data-confirm="{{ $confirmText }}"
                data-btn-ok-text="{{ $confirmTextOk }}"
                data-btn-ok-color="{{ $confirmColorOk }}"
                data-btn-cancel-text="{{ $confirmTextCancel }}"
                data-btn-cancel-color="{{ $confirmColorCancel }}"
                data-toggle="tooltip"
                title="{{ $title }}">
            <?php if($icon) { ?>
            <i class="{{ $icon }}"></i>
            <?php } ?>
            {{ $label }}
        </button>
    </form>
@endif