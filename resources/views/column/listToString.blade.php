@php
    /**
     * example: [
     *   *           'data'              => $data->purchases,
     *   *          'title'             => 'product_name',
     *   *          'titleAddition'     => 'variant_name',
     *   *          'delimiterAddition' => '-',
     *   *      ]
     * result: "productOne - variantThree"
     *
     * @var \Illuminate\Support\Collection $data
     * @var string                         $title
     * @var string                         $titleAddition
     * @var string                         $class
     * @var string                         $delimiter
     * @var string                         $delimiterAddition
     */
    if(!isset($data)) {
        return;
    }
    $title = isset($title) ? $title : 'title';
    $titleAddition = isset($titleAddition) ? $titleAddition : null;
    $class = isset($class) ? $class : 'label label-info';
    $delimiter = isset($delimiter) ? $delimiter : ' ';
    $delimiterAddition = isset($delimiterAddition) ? $delimiterAddition : '';
    $listTitle = [];
    /** * @param $model * * @return string */
    $fnGetString = function ($model) use ($title, $class, $titleAddition, $delimiterAddition) {
        if($model) {
            if($titleAddition) {
                $titleAddition = e($model->{$titleAddition});
                $titleAddition = $titleAddition ? ' ' . $delimiterAddition . ' ' . $titleAddition : $titleAddition;
            }
            return '<span class="' . e($class) . '">' . e($model->{$title}) . $titleAddition . '</span>';
        }
        return '';
    };
    if($data instanceof \Illuminate\Support\Collection || is_array($data)) {
        foreach($data as $model) {
            $listTitle[] = $fnGetString($model);
        }
    } else {
        $listTitle[] = $fnGetString($data);
    }
@endphp
{!! implode($delimiter, $listTitle) !!}