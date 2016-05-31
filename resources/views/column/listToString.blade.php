<?php
/**
 * @var \Illuminate\Support\Collection $data
 * @var string $title
 * @var string $class
 * @var string $delimiter
 */
$title = isset($title) ? $title : 'title';
$class = isset($class) ? $class : 'label label-info';
$delimiter = isset($delimiter) ? $delimiter : ' ';
$listTitle = [];
foreach ($data as $model) {
    if($model) {
        $listTitle[] = '<span class="' .e( $class) . '">' . e($model->{$title}) . '</span>';
    }
}
echo implode($delimiter, $listTitle);
?>