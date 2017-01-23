<?php
/**
 * @var string $id
 * @var string $class
 * @var string $jsClass
 * @var string $action
 * @var string $label
 * @var string $icon
 * @var string $url
 * @var string $method
 * @var string $confirmText
 * @var string $type
 * @var array  $options
 */
use Assurrussa\GridView\Support\ButtonItem;
use \Assurrussa\GridView\Support\Button;

$type = !empty($type) ? e($type) : null;
$method = !empty($method) ? e($method) : 'POST';
$confirmText = !empty($confirmText) ? e($confirmText) : \Assurrussa\GridView\GridView::trans('grid.clickDelete');
$id = !empty($id) ? e($id) : '';
$class = !empty($class) ? e($class) : 'btn btn-default btn-sm flat';
$jsClass = !empty($jsClass) ? e($jsClass) : '';
$action = !empty($action) ? e($action) : '';
$label = !empty($label) ? e($label) : '';
$title = !empty($title) ? e($title) : '';
$icon = !empty($icon) ? e($icon) : null;
$url = !empty($url) ? e($url) : '';
$attributes = [];
if(isset($options)) {
    foreach($options as $attribute => $value) {
        $attributes[] = $attribute . '="' . e($value) . '"';
    }
}
$linkButton = (
        $action === Button::TYPE_ACTION_SHOW
        || $action === Button::TYPE_ACTION_EDIT
        || $type === ButtonItem::TYPE_BUTTON_DEFAULT
        || $type === ButtonItem::TYPE_BUTTON_CREATE
        || $type === ButtonItem::TYPE_BUTTON_EXPORT
);
?>
<?php if($linkButton) { ?>
<a href="<?= $url; ?>"
   class="<?= $jsClass; ?> <?= $class; ?>"
   id="<?= $id; ?>"
   data-toggle="tooltip"
   title="<?= $title; ?>"
<?= implode(' ', $attributes); ?>>
    <?php if($icon) { ?>
    <i class="<?= $icon; ?>"></i>
    <?php } ?>
    <?= $label; ?>
</a>
<?php } elseif($action === Button::TYPE_ACTION_DELETE) { ?>
<form action="<?= $url; ?>" method="POST" style="display:inline-block;">
    <input type="hidden" name="_method" value="DELETE"/>
    {{ csrf_field() }}
    <button id="<?= $id; ?>"
            class="<?= !empty($jsClass) ? $jsClass : 'js-btnDelete'; ?> <?= $class; ?>"
            data-confirm-deleted="<?= $confirmText; ?>"
            data-toggle="tooltip"
            title="<?= $title; ?>">
        <?php if($icon) { ?>
        <i class="<?= $icon; ?>"></i>
        <?php } ?>
        <?= $label; ?>
    </button>
</form>
<?php }elseif($action === Button::TYPE_ACTION_RESTORE) { ?>
<form action="<?= $url; ?>" method="<?= $method; ?>" style="display:inline-block;">
    {{ csrf_field() }}
    <button id="<?= $id; ?>"
            class="<?= $jsClass; ?> <?= $class; ?>"
            data-toggle="tooltip"
            title="<?= $title; ?>">
        <?php if($icon) { ?>
        <i class="<?= $icon; ?>"></i>
        <?php } ?>
        <?= $label; ?>
    </button>
</form>
<?php } ?>