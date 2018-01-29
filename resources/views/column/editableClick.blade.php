@php
    /**
     * example:  AmiGridView::view('column.editableClick', [
     * *              'name'   => 'status',
     * *              'value'  => $data->status,
     * *              'title'  => 'title',
     * *              'type'   => 'select',
     * *              'url'    => route('....'),
     * *              'source' => [0 => 'item1', 1 => 'item2', ...],
     * *         ])->render();
     * result: "productOne - variantThree"
     *
     * @var string           $initJsEvent   Init js event
     * @var string           $initJsClass   Init js class
     * @var string           $class
     * @var string           $$name
     * @var string           $text
     * @var string|array|int $value
     * @var string           $url
     * @var string           $type          Type: text|textarea|select|checklist
     * @var int              $pk
     * @var array            $source        Source data for list.
     * @var string           $sourceCache   If true and source is string url - results will be cached for fields with the same source. Usefull for editable column
     *                            in grid to prevent extra requests.
     * @var string           $emptyText
     * @var string           $originalTitle Original title
     * @var string           $title
     * @var int              $rows
     * @var callable         $callback      function
     */
    $id = isset($id) ? $id : null;
    $initJsEvent = isset($initJsEvent) ? $initJsEvent : 'ready change';
    $initJsClass = isset($initJsClass) ? $initJsClass : 'js-editableClick';
    $class = isset($class) ? $class : 'inline-editable editable editable-click editable-empty';
    $name = isset($name) ? $name : 'name';
    $value = isset($value) ? $value : null;
    $url = isset($url) ? $url : 'title';
    $type = isset($type) ? $type : 'text';
    $pk = isset($pk) ? $pk : '1';
    $escape = isset($escape) ? $escape : true;
    $emptyText = isset($emptyText) ? $emptyText : 'Empty';
    $originalTitle = isset($originalTitle) ? $originalTitle : null;
    $title = isset($title) ? $title : '';
    $rows = isset($rows) ? $rows : 10;
    $callback = isset($callback) ? $callback : null;
    $sourceResult = null;
    if(isset($source)) {
        if(is_array($source)) {
            $array = [];
            foreach($source as $sourceValue => $sourceText) {
                $array[] = [
                        'value' => $sourceValue,
                        'text'  => $sourceText,
                ];
            }
            $sourceResult = json_encode($array);
        } elseif(is_string($source)) {
            $sourceResult = $source;
        }
    }
    if(is_array($value)) {
        $value = json_encode($value);
    }
@endphp
<a href="#"
   id="{{ $id }}"
   class="{{ $initJsClass }} {{ $class }}"
   data-escape="{{ $escape }}"
   data-name="{{ $name }}"
   data-value="{{ $value }}"
   data-url="{{ $url }}"
   data-type="{{ $type }}"
   data-pk="{{ $pk }}"
   data-emptytext="{{ $emptyText }}"
   @if($sourceResult)
   data-source="{{ $sourceResult }}"
   @endif
   @if($originalTitle)
   data-original-title="{{ $originalTitle }}"
   @endif
   @if($title)
   title="{{ $title }}"
   @endif
   @if($rows)
   data-rows="{{ $rows }}"
        @endif
@if($callback)
    {{ $callback }}
        @endif
>{{ $text }}</a>
{{--
<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script>
    $(function () {
        $(document).on('ready change click', function () {
            setTimeout(function () {
                $('.js-editableClick').editable({
                    validate: function (value) {
                        if ($.trim(value) == '') {
                            return 'This field is required';
                        }
                    }
                });
            }, 1000);
        });
</script>
--}}
