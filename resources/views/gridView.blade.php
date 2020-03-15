@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
$formElementBlockId = $data->getElementName();
$isAjax = $data->isAjax();
$isTrim = $data->isTrimLastSlash();
@endphp
<form id="{{ $formElementBlockId }}" action="{{ $data->formAction }}" class="AmiTableBox">
    @include('amiGrid::part.grid', ['data' => $data])
    <div id="js_loadingNotification" class="position-fixed-center" style="display: none">
        <div class="spinner" style="margin: 100px auto;">
            <div class="bounce1"></div>
            <div class="bounce2"></div>
            <div class="bounce3"></div>
        </div>
    </div>
</form>
@push('scripts')
    <script>
        window['{{ $formElementBlockId }}'] = new AmiGridJS({id: '{{ $formElementBlockId }}', ajax: !!'{{ $isAjax ? 'true' : '' }}', trimLastSlashCrop: !!'{{ $isTrim ? 'true' : '' }}'});
    </script>
@endpush
