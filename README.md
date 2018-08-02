# Mini grid view table for Laravel >= 5.5 #

## Install packages ##
1) `composer require assurrussa/grid-view-table`

3) If necessary, run the command `composer dump-autoload`
```
    php artisan vendor:publish --provider=Assurrussa\GridView\GridViewServiceProvider
```

### Setting ###
* In file `config/amigrid.php` required settings

### Routes ###

* If you do not need routing paths, then in the config `config/amigrid.php` just specify `FALSE`, for `routes`

### Include ###

in template use
```
{!! app(\Assurrussa\GridView\GridView::NAME)->render(['data' => $data]) !!}
or facade
{!! \AmiGridView::render(['data' => $data]) !!}

@stack('scripts')
```

### Done! ###

## AmiGridView DEMO

This is a sample app developed to showcase how the [assurrussa/grid-view-table](https://github.com/assurrussa/grid-view-table) package works

Check it out on heroku

- [DEMO](http://grid-view-table.herokuapp.com).
