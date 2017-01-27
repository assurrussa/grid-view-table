<?php
/**
 * @var \Assurrussa\GridView\Helpers\GridViewResult $data
 */
use Assurrussa\GridView\Support\Column;
?>
<tr>
    <?php foreach($data->headers as $header) { ?>
    <td>
        <?php if(count($header->filter)) { ?>
        <?php if($header->filter[Column::FILTER_KEY_MODE] === Column::FILTER_TYPE_STRING) { ?>
        <div class="js-textFilter input-group">
            <input type="text"
                   name="<?= $header->filter[Column::FILTER_KEY_NAME]; ?>"
                   value="<?= request()->get($header->filter[Column::FILTER_KEY_NAME]); ?>"
                   id="js-textFilter_<?= $header->filter[Column::FILTER_KEY_NAME]; ?>"
                   class="form-control"
                   data-mode="<?= $header->filter[Column::FILTER_KEY_MODE]; ?>">
        </div>
        <?php } elseif($header->filter[Column::FILTER_KEY_MODE] === Column::FILTER_TYPE_DATE) { ?>
        <div class="js-textFilter input-group date">
            <input type="text"
                   name="<?= $header->filter[Column::FILTER_KEY_NAME]; ?>"
                   value="<?= request()->get($header->filter[Column::FILTER_KEY_NAME]); ?>"
                   id="js-textFilter_<?= $header->filter[Column::FILTER_KEY_NAME]; ?>"
                   class="form-control"
                   data-mode="<?= $header->filter[Column::FILTER_KEY_MODE]; ?>">
            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
        </div>
        <?php } else { ?>
        <select name="<?= $header->filter[Column::FILTER_KEY_NAME]; ?>"
                id="js-selectFilter_<?= $header->filter[Column::FILTER_KEY_NAME]; ?>"
                class="js-selectFilter form-control"
                data-mode="<?= $header->filter[Column::FILTER_KEY_MODE]; ?>">
            <option disabled><?= \Assurrussa\GridView\GridView::trans('grid.selectFilter'); ?></option>
            <option value="" selected></option>
            <?php foreach($header->filter[Column::FILTER_KEY_DATA] as $key => $name) { ?>
            <option value="<?= $key; ?>"
            <?= request()->get($header->filter[Column::FILTER_KEY_NAME]) == $key ? 'selected' : ''; ?>
            >
                <?= $name; ?>
            </option>
            <?php } ?>
        </select>
        <?php } ?>
        <?php } ?>
    </td>
    <?php } ?>
</tr>