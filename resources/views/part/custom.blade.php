@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
$showHr = false;
@endphp
<div class="row">
    <div class="col-md-12">
        @foreach($data->inputCustoms as $inputCustom)
            {{ $showHr = true }}
            {!! $inputCustom !!}
        @endforeach
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        @foreach($data->buttonCustoms as $buttonCustom)
            {{ $showHr = true }}
            {!! $buttonCustom !!}
        @endforeach
    </div>
</div>
<div class="clearfix"></div>
@if($showHr)
<hr>
@endif