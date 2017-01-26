<?php
/**
 * @var \Assurrussa\GridView\Helpers\GridViewResult $data
 */
use Assurrussa\GridView\GridView;
use Assurrussa\GridView\Support\Column;

$sortName = 'id';
if($requestSortName = request()->get('sort')) {
    $sortName = $requestSortName;
}
$orderBy = Column::FILTER_ORDER_BY_ASC;
if($requestOrderBy = request()->get('by')) {
    $orderBy = (strtolower($requestOrderBy) === Column::FILTER_ORDER_BY_ASC)
            ? Column::FILTER_ORDER_BY_ASC
            : Column::FILTER_ORDER_BY_DESC;
}
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
    </div>
</div>