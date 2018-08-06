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
    </div>
</div>
<script>
    window.addEventListener('DOMContentLoaded',function(){if(!window.AmiGridJS && window.sessionStorage.getItem('amiGridAjax'))setTimeout(function(){window.location = window.location.href},100)});
</script>