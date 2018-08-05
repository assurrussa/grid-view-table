## Ami Grid View Table for Laravel

Live demo: [here](http://grid-view-table.herokuapp.com)

### Requirements

- php >= 7.1.3
- Laravel 5.5+
- axios
- vuejs

### Getting started

1 - Install
```
    composer require assurrussa/grid-view-table`
```

2 - Publish assets
```
    php artisan vendor:publish --provider=Assurrussa\GridView\GridViewServiceProvider
```

#### Setting
* In file `config/amigrid.php` required settings

### Creating grids

Example PostController:
```php
<?php

namespace App\Http\Controllers;

use App\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{

    /**
     * @var Post
     */
    private $post;

    /**
     * UserController constructor.
     *
     * @param Post $post
     */
    public function __construct(Post $post)
    {
        $this->post = $post;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     * @throws \Assurrussa\GridView\Exception\ColumnsException
     * @throws \Assurrussa\GridView\Exception\QueryException
     */
    public function index()
    {
        $title = 'Posts';
//        $data = $this->_getGridView()->getSimple(); // simple pagination
//        // or 
//        $data = $this->_getGridView()->get(); // full pagination
        $data = $this->_getGridView()->getSimple();
        if (request()->ajax() || request()->wantsJson()) {
            return $data->toHtml();
        }

        return view('post.index', compact('title', 'data'));
    }

    /**
     * @return \Assurrussa\GridView\GridView
     */
    private function _getGridView()
    {
        /** @var \Assurrussa\GridView\GridView $gridView */
        $query = $this->post->newQuery();
        $gridView = app('amiGrid');
        $gridView->setQuery($query)
            ->setSearchInput(true);

        // .......
        // .......
        // .......

        return $gridView;
    }
}
```

#### Include in template

example - layout.app:
```html
<!DOCTYPE html>
<html>
<head>
    <link href="{{ asset('vendor/grid-view/css/amigrid.css') }}" rel="stylesheet">
</head>
<body>

@yield('content')


{{--<script src="{{ asset('vendor/grid-view/js/amigrid.js') }}"></script>--}}
<script src="{{ asset('vendor/grid-view/js/amigrid-full.js') }}"></script>
@stack('scripts')
</body>
</html>
```

example - post.index:
```php
@php
    /**
    * @see \App\Http\Controllers\PostController::index
    * @var \Assurrussa\GridView\Helpers\GridViewResult $data
    */
@endphp
@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            {!! $data !!}
        </div>
    </div>
@endsection
```

### Rendering the grid

```php
    {!! $data !!}
```
For a quick demonstration, be sure to check out the demo [here](http://grid-view-table.herokuapp.com). The demo’s source code is also [available on github](https://github.com/assurrussa/grid-view-table-app).

### Updating local JS and CSS

```php
php artisan vendor:publish --provider="Assurrussa\GridView\GridViewServiceProvider" --tag=assets --force
```
You can also place this command in composer so that it is executed automatically on each update run.

example - composer config:
```php
"post-autoload-dump": [
    "Illuminate\\Foundation\\ComposerScripts::postAutoloadDump",
    "@php artisan package:discover",
    "@php artisan vendor:publish --provider=\"Assurrussa\\GridView\\GridViewServiceProvider\" --tag=assets --force"
]
```
