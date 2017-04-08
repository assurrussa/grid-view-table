<?php
/**
 * @var \Assurrussa\GridView\Helpers\GridViewResult $data
 */
?>
<div class="box">
    <form action=""></form>
    @include('amiGrid::part.custom')
    <div class="clearfix"></div>
    <hr>
    <div id="js-loadCatalogItems">
        @include('amiGrid::part.header')
        @include('amiGrid::part.table')
        @include('amiGrid::part.footer')
        <input type="submit" id="js-filterButtonSubmitForm" style="display:none;">
    </div>
</div>