@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
@endphp
<div class="row">
    <div class="col-md-12">
        @foreach($data->inputCustoms as $inputCustom)
            {!! $inputCustom !!}
        @endforeach
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        @foreach($data->buttonCustoms as $buttonCustom)
            {!! $buttonCustom !!}
        @endforeach
    </div>
</div>