<?php

namespace Assurrussa\GridView;

use Assurrussa\GridView\Exception\ColumnsException;
use Assurrussa\GridView\Exception\QueryException;
use Assurrussa\GridView\Interfaces\GridInterface;
use Assurrussa\GridView\Models\Model;
use Assurrussa\GridView\Support\ButtonItem;
use Assurrussa\GridView\Support\Button;
use Assurrussa\GridView\Support\Buttons;
use Assurrussa\GridView\Support\Column;
use Assurrussa\GridView\Support\ColumnCeil;
use Assurrussa\GridView\Support\Columns;
use Assurrussa\GridView\Support\EloquentPagination;

/**
 * Class GridView
 *
 * @package Assurrussa\GridView
 */
class GridView implements GridInterface
{

    const NAME = 'amiGrid';
    /**
     * @var string
     */
    public $id;
    /**
     * @var bool
     */
    public $visibleColumn;

    /** @var Columns */
    public $columns;

    /** @var Buttons */
    public $buttons;

    /** @var EloquentPagination */
    public $pagination;

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
        $this->pagination = new EloquentPagination();
        $this->setVisibleColumn(config('amigrid.visibleColumn', true));
    }

    /**
     * Added Builder Query
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Eloquent $query
     * @return $this
     */
    public function setQuery($query)
    {
        $this->_query = $query;
        $this->pagination->setQuery($this->_query);
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
    public function columnActions($action)
    {
        return $this->column()->setActions($action);
    }

    /**
     * @return Button
     */
    public function columnAction()
    {
        return new Button();
    }

    /**
     * @return ColumnCeil
     */
    public static function columnCeil()
    {
        return new ColumnCeil();
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
     * @return array|string
     * @throws ColumnsException
     * @throws QueryException
     */
    public function get()
    {
        if(!$this->_query) {
            throw new QueryException();
        }
        $this->_setRequest()
            ->_hasColumns();

        $this->pagination->setColumns($this->columns);

        if(!$this->columns->count()) {
            throw new ColumnsException();
        }

        $page = (int)$this->_request->pull('page', 1);
        $orderBy = $this->_request->pull('orderBy', 'ASC');
        $search = $this->_request->pull('search', '');
        $limit = (int)$this->_request->pull('count', 10);
        $sortName = $this->_request->pull('sortName', 'id');

        $this->filterScopes();
        $this->filterSearch($search);
        $this->filterOrderBy($sortName, $orderBy);

        $gridViewResult = new \Assurrussa\GridView\Helpers\GridViewResult();
        $gridViewResult->id = $this->getId();
        $gridViewResult->data = $this->pagination->get($page, $limit);
        $gridViewResult->pagination = $this->pagination->toArray();
        $gridViewResult->headers = $this->columns->toArray();
        $gridViewResult->createButton = $this->buttons->getButtonCreate();
        $gridViewResult->exportButton = $this->buttons->getButtonExport();
        $gridViewResult->customButton = $this->buttons->render();
        $gridViewResult->filter = $this->_request->toArray();
        $gridViewResult->page = $page;
        $gridViewResult->orderBy = $orderBy;
        $gridViewResult->search = $search;
        $gridViewResult->count = $limit;
        $gridViewResult->sortName = $sortName;
        return $gridViewResult->toArray();
    }

    /**
     * @param string $id
     * @return GridView
     */
    public function setId(string $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        if(!$this->id) {
            $this->setId(self::NAME . '_' . 1);
        }
        return $this->id;
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
        $this->columnActions(function ($data) {
            $pathNameForModel = strtolower(str_plural(camel_case(class_basename($data))));
            $buttons = [];
            $buttons[] = $this->columnAction()->setActionDelete('delete', [$pathNameForModel, $data->id]);
            $buttons[] = $this->columnAction()->setActionShow('show', [$pathNameForModel, $data->id])->setHandler(function ($data) {
                return false;
            });
            $buttons[] = $this->columnAction()->setActionEdit('edit', [$pathNameForModel, $data->id]);
            return $buttons;
        });
        $this->button()->setButtonCreate();
    }
}