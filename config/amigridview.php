<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Grid Routes
    |--------------------------------------------------------------------------
    |
    |  routes по умолчанию стоит TRUE
    |
    */
    'routes' => true,

    /*
    |--------------------------------------------------------------------------
    | Base Namespace for models
    |--------------------------------------------------------------------------
    |
    |  Необходимо указать путь namespace до моделей, для динамичного нахождения моделей
    |
    */
    'namespace' => '\Assurrussa\GridView\Models\\',

    /*
    |--------------------------------------------------------------------------
    | Base Prefix for GridView
    |--------------------------------------------------------------------------
    |
    |  Необходимо указать префикс по скольку разбор модели будет отрабатываться в зависимости от пути
    |
    */
    'prefix' => 'backend',

    /*
    |--------------------------------------------------------------------------
    | Middleware group
    |--------------------------------------------------------------------------
    |
    |  Если нужно вы можете указать middleware группы для роутов
    |
    */
    'middleware' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Path base View
    |--------------------------------------------------------------------------
    |
    |  Если нужно вы можете поменять на своё view
    |
    */
    'pathView' => 'gridView',

];