<?php
/**
 * @var \Assurrussa\GridView\Helpers\GridViewResult $data
 */
?>
<div class="row">
    <div class="col-md-12">
        <?php foreach($data->inputCustoms as $inputCustom) { ?>
            <?= $inputCustom; ?>
        <?php } ?>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <?php foreach($data->buttonCustoms as $buttonCustom) { ?>
            <?= $buttonCustom; ?>
        <?php } ?>
    </div>
</div>