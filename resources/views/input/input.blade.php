@php
    /**
     * @var string $type
     * @var string $id
     * @var string $class
     * @var string $name
     * @var string $value
     * @var array  $options
     */

    $type = !empty($type) ? $type : 'hidden';
    $id = !empty($id) ? $id : '';
    $class = !empty($class) ? $class : '';
    $name = !empty($name) ?$name : '';
    $value = !empty($value) ? $value : '';
    $attributes = [];
    if(isset($options)) {
        foreach($options as $attribute => $value) {
            $attributes[] = $attribute . '="' . $value . '"';
        }
    }
@endphp
<input name="{{ $name }}"
       type="{{ $type }}"
       value="{{ $value }}"
       id="{{ $id }}"
       class="{{ $class }}"
{{ implode(' ', $attributes) }} />