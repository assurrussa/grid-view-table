<?php
/**
 * @var \Assurrussa\GridView\Helpers\GridViewResult $data
 */
use Assurrussa\GridView\GridView;
?>
<div class="row">
    <div class="col-sm-6">
        <label>
            <select id="js-amiSelectCount" name="count" class="input-sm">
                <?php foreach($data->counts as $key => $value) { ?>
                <option <?= request()->get('count') == $key ? 'selected' : ''; ?> value="<?= $key; ?>">
                    <?= $value; ?>
                </option>
                <?php } ?>
            </select>
        </label>
        <?= $data->buttonCreate; ?>
        <?= $data->buttonExport; ?>
    </div>
    <div class="col-sm-6">
        <div class="pull-right">
            <a id="js-filterSearchClearSubmit"
               class="btn btn-default btn-sm"
               data-toggle="tooltip"
               data-original-title="<?= GridView::trans('grid.clearFilter'); ?>"
               href="">
                <i class="fa fa-times"></i>
                <?= GridView::trans('grid.clear'); ?>
            </a>
            <?php if($data->searchInput) { ?>
            <label>
                <input type="text"
                       id="js-amiSearchInput"
                       name="search"
                       value="<?= request()->get('search'); ?>"
                       class="form-control input-sm"
                       placeholder="search">
            </label>
            <?php } ?>
        </div>
    </div>
</div>