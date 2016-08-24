# AmiCMS mini GridView for Laravel 5.2 #

Mini gridView for laravel feat Vue.js.

## Install packages ##
1) Добавить в `composer.json` строки и сделать `composer update`
```
        ...
        "require": {
        ...
            "assurrussa/grid-view-vue": "dev-master"
        ...
```
или `composer require assurrussa/grid-view-vue`

2) Добавить в конфиг `config/app.php`
```
    'providers' => [
        ...
        Assurrussa\GridView\GridViewServiceProvider::class,
        ...
    ],

    'aliases' => [
        ...
        'AmiGridView' => Assurrussa\GridView\Facades\GridViewFacade::class,
        ...
    ],
```
3) Выполнить команду `composer dump-autoload`. После набрать команду
```
    php artisan vendor:publish --provider=Assurrussa\GridView\GridViewServiceProvider
```
при необходимости произвести команду `composer dump-autoload`.

### Настройки ###
* в файле `config/amigridview.php` необходимые настройки

### Routes ###

* Если вам не нужны пути роутов, то в конфиге `config/amigridview.php` просто укажите `FALSE`, для `routes`

### Использование ###

```
{!! \AmiGridView::render() !!}
```

### Готово! ###

### Example ###

```
    /**
     * @return array
     */
    public function sync()
    {
        $query = new Product();
        $query->with(Product::REL_CATALOG, Product::REL_BRAND);
        $columns = AmiGridView::columns(function ($grid) {
            /** @var \Assurrussa\GridView\Support\GridColumns $grid */
            $catalogs = Catalog::pluck('title', 'id');
            $brands = Brand::pluck('name', 'id');
            $columns = [];
            $columns[] = $grid->column()->setKey('id')->setValue(trans('adminCms.label.id'))->setSort(true);
            $columns[] = $grid->column()->setKey('catalogs.title')->setValue(trans('adminCms.menu.catalog'))
                ->setSort(false)->setScreening(true)->setFilter('catalog_id', $catalogs)->setHandler(function ($data) {
                    /** @var Product $data */
                    return view('amigridview::column.listToString', [
                        'data' => $data->catalogs,
                    ])->render();
                });
            $columns[] = $grid->column()->setKey('brands.name')->setValue(trans('adminCms.menu.brand'))
                ->setSort(false)->setScreening(true)->setFilter('brand_id', $brands)->setHandler(function ($data) {
                    /** @var Product $data */
                    $brand = $data->brands->name;
                    $link = view('amigridview::column.filterButton', [
                        'name' => 'brand_id',
                        'id'   => $data->brands->id,
                    ])->render();
                    return $brand . ' ' . $link;
                });
            $columns[] = $grid->column()->setKey('title')->setValue(trans('adminCms.label.title'))->setSort(true);
            $columns[] = $grid->column()->setKey('slug')->setValue(trans('adminCms.label.slug'))->setSort(true);
            $columns[] = $grid->column()->setKey('preview')->setValue(trans('adminCms.label.preview'))->setSort(false)
                ->setScreening(true)->setHandler(function ($data) {
                    /** @var Product $data */
                    return view('amigridview::column.linkImage', [
                        'link'  => $data->preview,
                        'title' => $data->title,
                    ])->render();
                });
            $columns[] = $grid->column()->setKeyAction()->setActions(function ($data) use ($grid) {
                /** @var Product $data */
                $buttons = [];
                $buttons[] = $grid->button()
                    ->setAction('delete')
                    ->setLabel('deleted')
                    ->setRoute('admin.product.delete', [$data->id])
                    ->setIcon('fa-cancel')
                    ->setHandler(function ($data) {
                        /** @var Product $data */
                        return true;
                    });
                $buttons[] = $grid->button()
                    ->setAction('edit')
                    ->setLabel('edit')
                    ->setRoute('admin.product.edit', [$data->id])
                    ->setIcon('fa-show');
                return $buttons;
            });
            return $grid->setColumns($columns);
        });
        return $columns->query($query)->get();
    }
```
![exapmle](https://github.com/assurrussa/grid-view-vue/blob/master/example.png)
