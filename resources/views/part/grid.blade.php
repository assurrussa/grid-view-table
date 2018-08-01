@php
    /**
     * @var \Assurrussa\GridView\Helpers\GridViewResult $data
     */
@endphp
<div class="box">
    <form action=""></form>
    <div class="clearfix"></div>
    @include('amiGrid::part.custom')
    <div id="js_loadCatalogItems">
        @include('amiGrid::part.header')
        @include('amiGrid::part.table')
        @include('amiGrid::part.footer')
        <input type="submit" id="js_filterButtonSubmitForm" style="display:none;">
    </div>
</div>