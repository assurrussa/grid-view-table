<?php
/**
 * @var \Assurrussa\GridView\Helpers\GridViewResult $data
 */
use Assurrussa\GridView\GridView;
?>
<div class="row">
    <div class="col-sm-6">
        <div>
            <?= GridView::trans('grid.gridCounts', [
                    'from'  => $data->data->firstItem(),
                    'to'    => $data->data->lastItem(),
                    'total' => $data->data->total(),
            ]); ?>
        </div>
    </div>
    <div class="col-sm-6">
        <ul class="js-filterSearchPagination pagination pagination-sm pull-right">

            <?= $data->pagination; ?>

        </ul>
    </div>
</div>