# Mini grid view table for Laravel >= 5.5 #

## Install packages ##
1) `composer require assurrussa/grid-view-table`

3) If necessary, run the command `composer dump-autoload`
```
    php artisan vendor:publish --provider=Assurrussa\GridView\GridViewServiceProvider
```

### Setting ###
* In file `config/amigrid.php` required settings

### Include ###

in template use
```
{!! app(\Assurrussa\GridView\GridView::NAME)->render(['data' => $data]) !!}
or facade
{!! \AmiGridView::render(['data' => $data]) !!}

<!-- AmiGridView -->
<link href="{{ asset('vendor/grid-view/css/amigrid.css') }}" rel="stylesheet">
<script src="{{ asset('vendor/grid-view/js/amigrid.js') }}"></script>
<!-- AmiGridView -->
@stack('scripts')
```

### Done! ###

## AmiGridView DEMO

This is a sample app developed to showcase how the [assurrussa/grid-view-table](https://github.com/assurrussa/grid-view-table) package works

Check it out on heroku

- [DEMO](http://grid-view-table.herokuapp.com).
