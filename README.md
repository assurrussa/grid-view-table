# Mini grid view table for Laravel >= 5.5 #

## Install packages ##
1) `composer require assurrussa/grid-view-table`

2) Add to config `config/app.php` 
```
    'providers' => [
        Assurrussa\GridView\GridViewServiceProvider::class,
    ],
    'aliases' => [
        'AmiGridView' => Assurrussa\GridView\Facades\GridViewFacade::class,
    ],
```
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

### Example ###

```
    /**
     * @return array
     */
    public function index()
    {
        $model = new Entity();
        $query = $model->newQuery()->with('catalogs', 'brand');

        // create new model GridView
        $gridView = app(\Assurrussa\GridView\GridView::NAME);
        // set Builder Query
        $gridView->query($query);
        
        // search input for table
        $gridView->setSearchInput(false);

        $catalogs = Catalog::pluck('title', 'id');
        $brands = Brand::pluck('name', 'id');

        // added column
        $gridView->column()->setKey('id')->setValue(trans('app.label.id'))->setSort(true);
        $gridView->column()->setCheckbox();
        $gridView->column()->setKey('catalogs.title')->setValue(trans('app.menu.catalog'))
            ->setSort(false)->setScreening(true)->setFilter('catalog_id', $catalogs)->setHandler(function ($data) {
                /** @var Entity $data */
                return amiGridColumnCeil()->listToString($data->catalogs);
            });
        $gridView->column()->setKey('brands.name')->setValue(trans('app.menu.brand'))
            ->setSort(false)->setScreening(true)->setFilter('brand_id', $brands)->setHandler(function ($data) {
                /** @var Entity $data */
                $brand = $data->brand->name;
                $link = amiGridColumnCeil()->filterButton($data->brand->id, 'brand_id');
                return $brand . ' ' . $link;
            });
        $gridView->column()->setKey('title')->setValue(trans('app.label.title'))->setSort(true);
        $gridView->column()->setKey('slug')->setValue(trans('app.label.slug'))->setSort(true);
        $gridView->column()->setKey('preview')->setValue(trans('app.label.preview'))->setSort(false)
            ->setScreening(true)->setHandler(function ($data) {
                /** @var Entity $data */
                return amiGridColumnCeil()->image($data->entityInfo->preview, $data->entityInfo->title);    
            });


        // added column actions
        $gridView->columnActions(function ($data) use ($gridView) {
            /** @var Entity $data */
            $buttons = [];
            $buttons[] = $gridView->columnAction()->setActionDelete('admin.entity.delete', [$data->id]);
            $buttons[] = $gridView->columnAction()->setActionShow('admin.entity.show', [$data->id])->setHandler(function ($data) {
                /** @var Entity $data */
                return false;
            });
            $buttons[] = $gridView->columnAction()->setActionEdit('admin.entity.edit', [$data->id]);
            return $buttons;
        });

        // added buttons for table
        $gridView->button()->setButtonCreate(route('admin.entity.create'));
        $gridView->button()->setButtonExport();
        $gridView->button()->setButtonCheckboxAction(route('admin.entity.custom'));

        // return result
        $data = $gridView->get();
        if(request()->ajax() || request()->wantsJson()) {
            return amiGrid()->render(compact('data'));
        }
        return view('index', compact('data'));
    }
```
![exapmle](https://github.com/assurrussa/grid-view-vue/blob/master/example.png)
