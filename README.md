# AmiCMS mini GridView for Laravel >= 5.3 #

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
* в файле `config/amigrid.php` необходимые настройки

### Routes ###

* Если вам не нужны пути роутов, то в конфиге `config/amigrid.php` просто укажите `FALSE`, для `routes`

### Использование ###

```
{!! amiGrid()->render() !!}
or
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
        $model = new Entity();
        $query = $model->newQuery()->with('catalogs', 'brand');

        // create new model GridView
        $gridView = amiGrid();
        // set Builder Query
        $gridView->query($query);

        $catalogs = Catalog::pluck('title', 'id');
        $brands = Brand::pluck('name', 'id');

        // added column
        $gridView->column()->setKey('id')->setValue(trans('app.label.id'))->setSort(true);
        $gridView->column()->setCheckbox();
        $gridView->column()->setKey('catalogs.title')->setValue(trans('app.menu.catalog'))
            ->setSort(false)->setScreening(true)->setFilter('catalog_id', $catalogs)->setHandler(function ($data) {
                /** @var Entity $data */
                return GridView::view('column.listToString', [
                    'data' => $data->catalogs,
                ])->render();
            });
        $gridView->column()->setKey('brands.name')->setValue(trans('app.menu.brand'))
            ->setSort(false)->setScreening(true)->setFilter('brand_id', $brands)->setHandler(function ($data) {
                /** @var Entity $data */
                $brand = $data->brand->name;
                $link = GridView::view('column.filterButton', [
                    'name' => 'brand_id',
                    'id'   => $data->brand->id,
                ])->render();
                return $brand . ' ' . $link;
            });
        $gridView->column()->setKey('title')->setValue(trans('app.label.title'))->setSort(true);
        $gridView->column()->setKey('slug')->setValue(trans('app.label.slug'))->setSort(true);
        $gridView->column()->setKey('preview')->setValue(trans('app.label.preview'))->setSort(false)
            ->setScreening(true)->setHandler(function ($data) {
                /** @var Entity $data */
                return GridView::view('column.linkImage', [
                    'link'  => $data->entityInfo->preview,
                    'title' => $data->entityInfo->title,
                ])->render();
            });


        // added column actions
        $gridView->columnButtons(function ($data) use ($gridView) {
            /** @var Entity $data */
            $buttons = [];
            $buttons[] = $gridView->columnButton()->setActionDelete('admin.entity.delete', [$data->id]);
            $buttons[] = $gridView->columnButton()->setActionShow('admin.entity.show', [$data->id])->setHandler(function ($data) {
                /** @var Entity $data */
                return false;
            });
            $buttons[] = $gridView->columnButton()->setActionEdit('admin.entity.edit', [$data->id]);
            return $buttons;
        });

        // added buttons for table
        $gridView->button()->setButtonCreate(route('admin.entity.create'));
        $gridView->button()->setButtonExport();
        $gridView->button()->setButtonCheckboxAction();

        // return result
        return $gridView->get();
    }
```
![exapmle](https://github.com/assurrussa/grid-view-vue/blob/master/example.png)
