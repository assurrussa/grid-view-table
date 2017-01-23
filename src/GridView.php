<?php

namespace Assurrussa\GridView;

use Assurrussa\GridView\Interfaces\GridInterface;
use Assurrussa\GridView\Models\Model;
use Assurrussa\GridView\Support\ButtonItem;
use Assurrussa\GridView\Support\Button;
use Assurrussa\GridView\Support\Buttons;
use Assurrussa\GridView\Support\Column;
use Assurrussa\GridView\Support\Columns;
use Assurrussa\GridView\Support\Pagination;

/**
 * Class GridView
 *
 * @package Assurrussa\GridView
 */
class GridView implements GridInterface
{

    const NAME = 'amiGrid';
    /**
     * @var bool
     */
    public $visibleColumn;

    /** @var Columns */
    public $columns;

    /** @var Buttons */
    public $buttons;

    /** @var \Illuminate\Support\Collection $_request */
    private $_request;

    /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|static */
    private $_query;

    /**
     * GridView constructor.
     */
    public function __construct()
    {
        $this->columns = new Columns();
        $this->buttons = new Buttons();
        $this->setVisibleColumn(config('amigrid.visibleColumn', true));
    }

    /**
     * Added Builder Query
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Eloquent $query
     * @return $this
     */
    public function query($query)
    {
        $this->_query = $query;
        return $this;
    }

    /**
     * @return ButtonItem
     */
    public function button()
    {
        $button = new ButtonItem();
        $button->setQuery($this->_query);
        $this->buttons->setButton($button);
        return $button;
    }

    /**
     * @return Column
     */
    public function column()
    {
        $column = new Column();
        $this->columns->setColumn($column);
        return $column;
    }

    /**
     * @param Button[]|\Closure $action
     * @return Column
     */
    public function columnButtons($action)
    {
        return $this->column()->setActions($action);
    }

    /**
     * @return Button
     */
    public function columnButton()
    {
        return new Button();
    }

    /**
     * @param string $path
     * @param array  $data
     * @param array  $mergeData
     * @return mixed
     */
    public function render($path = 'gridView', $data = [], $mergeData = [])
    {
        return static::view($path, $data, $mergeData);
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public static function view($view = null, $data = [], $mergeData = [])
    {
        return view(self::NAME . '::' . $view, $data, $mergeData);
    }

    /**
     * Translate the given message.
     *
     * @param  string $id
     * @param  array  $parameters
     * @param  string $domain
     * @param  string $locale
     * @return \Symfony\Component\Translation\TranslatorInterface|string
     */
    public static function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        return trans(self::NAME . '::' . $id, $parameters, $domain, $locale);
    }

    /**
     * Return get result
     *
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    public function get()
    {
        $this->_setRequest()
            ->_hasColumns();

        $search = $this->_request->pull('search', '');
        $page = (int)$this->_request->pull('page', 1);
        $limit = (int)$this->_request->pull('count', 10);
        $sortName = $this->_request->pull('sortName', 'id');
        $orderBy = $this->_request->pull('orderBy', 'ASC');

        $this->filterScopes();
        $this->filterSearch($search);
        $this->filterOrderBy($sortName, $orderBy);
        $data = $this->getPagination($page, $limit);

        return [
            'data'         => $data,
            'pagination'   => $this->getPaginationToArray($data),
            'headers'      => $this->columns->toArray(),
            'createButton' => $this->buttons->getButtonCreate(),
            'exportButton' => $this->buttons->getButtonExport(),
            'customButton' => $this->buttons->render(),
            'page'         => $page,
            'orderBy'      => $orderBy,
            'search'       => $search,
            'count'        => $limit,
            'sortName'     => $sortName,
            'filter'       => $this->_request->toArray(),
        ];
    }

    /**
     * Returns a collection in view of pagination
     * Возвращает коллекцию в виде пагинации
     *
     * @param int $page
     * @param int $limit
     */
    public function getPagination($page, $limit = 10)
    {
        /**
         * @var \Illuminate\Support\Collection $data
         */
        $countTotal = $this->_query->count();
        $this->_query->skip(($limit * $page) - $limit);
        $this->_query->limit($limit);
        $data = collect();
        foreach($this->_query->get() as $key => $instance) {
            $_listRow = [];
            foreach($this->columns->getColumns() as $column) {
                $_listRow[$column->getKey()] = $column->getValues($instance);
            }
            $buttons = $this->columns->filterActions($instance);
            if(count($buttons)) {
                $_listRow = array_merge($_listRow, [Column::ACTION_NAME => implode('', $buttons)]);
            }
            $data->offsetSet($key, $_listRow);
        }
        return new \Illuminate\Pagination\LengthAwarePaginator($data, $countTotal, $limit, $page, [
            'path'     => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /**
     * Return pagination to Array
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $data
     * @return array
     */
    public function getPaginationToArray($data)
    {
        return (new Pagination)->setData($data)->toArray();
    }

    /**
     * @param boolean $visibleColumn
     * @return GridView
     */
    public function setVisibleColumn(bool $visibleColumn)
    {
        $this->visibleColumn = $visibleColumn;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isVisibleColumn(): bool
    {
        return $this->visibleColumn;
    }

    /**
     * @return $this
     */
    protected function _setRequest()
    {
        if(!$this->_request) {
            $this->_request = collect(request()->all());
        }
        return $this;
    }

    /**
     * Very simple filtration scopes.<br><br>
     * Очень простая фильтрация скоупов.<br><br>
     *
     * If you want to filter Scope by id or something, be sure to create the scope of the following form.<br><br>
     * Если необходимо отфильтровать скоупы по id чему либо, обязательно нужно создать scope следующего вида.<br><br>
     * Example:
     * * link to filter - `catalog_id=*`
     * * method - `public function scopeCatalogId($int) {}`
     *
     * @return \Illuminate\Support\Collection
     */
    protected function filterScopes()
    {
        if(count($this->_request) > 0) {
            foreach($this->_request as $scope => $value) {
                if(!empty($value)) {
                    //checked scope method for model
                    if(method_exists($this->_query->getModel(), 'scope' . camel_case($scope))) {
                        $this->_query->{camel_case($scope)}($value);
                    } else {
                        $values = explode(',', $value);
                        if(count($values) > 1) {
                            $this->filterSearch($scope, $values[0], '>');
                            $this->filterSearch($scope, $values[1], '<');
                        } else {
                            $this->filterSearch($scope, $value, 'like', '%', '%');
                        }
                    }
                }
            }
            $this->_query->addSelect($this->_query->getModel()->getTable() . '.*');
        }
        return $this->_request;
    }

    /**
     * The method filters the data according<br><br>
     * Метод фильтрует данные по словам<br>
     *
     * @param string|int $search
     * @param null       $value       word
     * @param string     $delimiter   equal sign - '=', 'like' ...
     * @param string     $beforeValue First sign before value
     * @param string     $afterValue  Last sign after value
     */
    protected function filterSearch($search, $value = null, $delimiter = '=', $beforeValue = '', $afterValue = '')
    {
        if($search) {
            $value = trim($value);
            $search = trim($search);
            /** @var Model $model */
            $model = $this->_query->getModel();
            // поиск по словам
            if(is_string($search) || is_numeric($search)) {
                $this->_query->where(function ($query) use ($model, $search, $value, $delimiter, $beforeValue, $afterValue) {
                    /** @var \Illuminate\Database\Eloquent\Builder $query */
                    $tableName = $model->getTable();
                    if($value) {
                        if(Model::hasColumn($model, $search)) {
                            $query->orWhere($tableName . '.' . $search, $delimiter, $beforeValue . $value . $afterValue);
                        }
                    } else {
                        foreach(\Schema::getColumnListing($tableName) as $column) {
                            if($this->hasFilterExecuteForCyrillicColumn($search, $column)) {
                                continue;
                            }
                            if(Model::hasColumn($model, $column)) {
                                $query->orWhere($tableName . '.' . $column, 'like', '%' . $search . '%');
                            }
                        }
                    }
                });
            }
        }
    }

    /**
     * @param string $sortName
     * @param string $orderBy
     */
    protected function filterOrderBy($sortName, $orderBy)
    {
        if($sortName) {
            $this->_query->orderBy($this->_query->getModel()->getTable() . '.' . $sortName, $orderBy);
        }
    }

    /**
     * Because of problems with the search Cyrillic, crutch.<br><br>
     * Из-за проблем поиска с кириллицей, костыль.
     *
     * @param string $search
     * @param string $column
     */
    protected function hasFilterExecuteForCyrillicColumn($search, $column)
    {
        if(!preg_match("/[\w]+/i", $search) && \Assurrussa\GridView\Enums\FilterEnum::hasFilterExecuteForCyrillicColumn($column)) {
            return true;
        }
        return false;
    }

    /**
     * Check exists prepared columns<br><br>
     * Проверка существует ли предварительная подготовка данных.
     */
    protected function _hasColumns()
    {
        if(!$this->columns->count()) {
            $this->_prepareColumns();
        }
    }

    /**
     * The method takes the default column for any model<br><br>
     * Метод получает колонки по умолчанию для любой модели
     */
    protected function _prepareColumns()
    {
        $model = $this->_query->getModel();
        $lists = \Schema::getColumnListing($model->getTable());
        if($this->isVisibleColumn()) {
            $lists = array_diff($lists, $model->getHidden());
        }
        foreach($lists as $key => $list) {
            $this->column()
                ->setKey($list)
                ->setValue($list)
                ->setDateActive(true)
                ->setSort(true);
        }
        $this->columnButtons(function ($data) {
            $pathNameForModel = strtolower(str_plural(camel_case(class_basename($data))));
            $buttons = [];
            $buttons[] = $this->columnButton()->setActionDelete('delete', [$pathNameForModel, $data->id]);
            $buttons[] = $this->columnButton()->setActionShow('show', [$pathNameForModel, $data->id])->setHandler(function ($data) {
                return false;
            });
            $buttons[] = $this->columnButton()->setActionEdit('edit', [$pathNameForModel, $data->id]);
            return $buttons;
        });
    }
}