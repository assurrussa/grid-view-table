<?php
/**
 * @var \Assurrussa\GridView\Helpers\GridViewResult $data
 */
use Assurrussa\GridView\Support\Column;

$sortName = $data->sortName;
$orderBy = $data->orderBy;
?>
<div class="content-box">
    <input type="hidden" id="js-amiOrderBy" name="by" value="<?= $orderBy; ?>">
    <input type="hidden" id="js-amiSortName" name="sort" value="<?= $sortName; ?>">
    <table class="table table-stripped table-bordered table-hover table-condensed">
        <thead>
        <tr>
            <?php foreach($data->headers as $header) {
            if ($header->key === Column::ACTION_STRING_TR) {
                continue;
            }
            if ($sortName == $header->key) {
                $orderByResult = $header->sort ? 'arrow ' . $orderBy : '';
            } else {
                $orderByResult = $header->sort ? 'arrow ' . Column::FILTER_ORDER_BY_ASC : '';
            }
            ?>
            <th id="js-amiTableHeader<?= $data->getElementName($header->key); ?>"
                class="<?= $sortName === $header->key ? 'active' : ''; ?> js-amiTableHeader"
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
        <?php foreach($data->data->items() as $item) {
        $classString = '';
        foreach ($data->headers as $header) {
            if ($header->key === Column::ACTION_STRING_TR) {
                $classString = e($item[$header->key]);
            }
        }
        ?>
        <tr class="js-loaderBody <?= $classString; ?>">
            <?php foreach($data->headers as $header) {
            if ($header->key === Column::ACTION_STRING_TR) {
                continue;
            } ?>
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