<?php
/**
 * @var \Assurrussa\GridView\Helpers\GridViewResult $data
 */
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
<div class="content-box">
    <input type="hidden" id="js-amiOrderBy" name="by" value="<?= $orderBy; ?>">
    <input type="hidden" id="js-amiSortName" name="sort" value="<?= $sortName; ?>">
    <table class="table table-stripped table-bordered table-hover table-condensed">
        <thead>
        <tr>
            <?php foreach($data->headers as $header) {
            if($sortName == $header->key) {
                $orderByResult = $header->sort ? 'arrow ' . $orderBy : '';
            } else {
                $orderByResult = $header->sort ? 'arrow ' . Column::FILTER_ORDER_BY_ASC : '';
            }
            ?>
            <th id="js-amiTableHeader<?= $data->getElementName($header->key); ?>"
                class="<?= $requestSortName === $header->key ? 'active' : ''; ?> js-amiTableHeader"
                data-name="<?= $header->key; ?>">
                <?= ($header->screening && ($header->key === 'checkbox'))
                        ? $header->value
                        : e($header->value); ?>
                <span class="<?= $orderByResult; ?>"></span>
            </th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <!-- ============================== FILTER =============================== -->
        @include('amiGrid::part.tableFilter')
        <!-- ============================== FILTER =============================== -->

        <!-- ============================== BODY =============================== -->
        <?php foreach($data->data->items() as $item) { ?>
        <tr class="js-loaderBody">
            <?php foreach($data->headers as $header) { ?>
            <td>
                <?= $header->screening ? $item[$header->key] : e($item[$header->key]); ?>
            </td>
            <?php } ?>
        </tr>
        <?php } ?>
        <!-- ============================== BODY =============================== -->
        </tbody>
    </table>


</div>