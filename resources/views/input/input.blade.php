<?php
/**
 * @var string $type
 * @var string $id
 * @var string $class
 * @var string $name
 * @var string $value
 * @var array  $options
 */

$type = !empty($type) ? e($type) : 'hidden';
$id = !empty($id) ? e($id) : '';
$class = !empty($class) ? e($class) : '';
$name = !empty($name) ? e($name) : '';
$value = !empty($value) ? e($value) : '';
$attributes = [];
if(isset($options)) {
    foreach($options as $attribute => $value) {
        $attributes[] = $attribute . '="' . e($value) . '"';
    }
}
?>
<input name="<?= $name; ?>"
       type="<?= $type; ?>"
       value="<?= $value; ?>"
       id="<?= $id; ?>"
       class="<?= $class; ?>"
<?= implode(' ', $attributes); ?> />