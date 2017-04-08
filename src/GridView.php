<?php

namespace Assurrussa\GridView;

use Assurrussa\GridView\Exception\ColumnsException;
use Assurrussa\GridView\Exception\QueryException;
use Assurrussa\GridView\Export\ExportData;
use Assurrussa\GridView\Interfaces\GridInterface;
use Assurrussa\GridView\Models\Model;
use Assurrussa\GridView\Support\ButtonItem;
use Assurrussa\GridView\Support\Button;
use Assurrussa\GridView\Support\Buttons;
use Assurrussa\GridView\Support\Column;
use Assurrussa\GridView\Support\ColumnCeil;
use Assurrussa\GridView\Support\Columns;
use Assurrussa\GridView\Support\EloquentPagination;
use Assurrussa\GridView\Support\Input;
use Assurrussa\GridView\Support\Inputs;

/**
 * Class GridView
 *
 * @package Assurrussa\GridView
 */
class GridView implements GridInterface
{

    const NAME = 'amiGrid';

    /** @var string */
    public $id;
    /** @var int */
    public $page;
    /** @var int */
    public $limit;
    /** @var string */
    public $orderBy;
    public $orderByDefault = Column::FILTER_ORDER_BY_ASC;
    /** @var string */
    public $search;
    /** @var bool */
    public $searchInput = false;
    /** @var string */
    public $sortName;
    public $sortNameDefault = 'id';
    /** @var array */
    public $counts;
    /** @var bool */
    public $export = false;
    /**
     * @var bool
     */
    public $visibleColumn;

    /** @var Columns */
    public $columns;

    /** @var Buttons */
    public $buttons;

    /** @var Inputs */
    public $inputs;

    /** @var EloquentPagination */
    public $pagination;

    /** @var array */
    private $_config;

    /** @var \Illuminate\Support\Collection $_request */
    private $_request;

    /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|static */
    private $_query;

    /**
     * GridView constructor.
     */
    public function __construct(
        Columns $columns,
        Buttons $buttons,
        Inputs $inputs,
        EloquentPagination $eloquentPagination
    ) {
        $this->_config = config('amigrid');
        $this->columns = $columns;
        $this->buttons = $buttons;
        $this->inputs = $inputs;
        $this->pagination = $eloquentPagination;
        $this->counts = $this->getConfig('counts', [
            10  => 10,
            25  => 25,
            100 => 100,
            200 => 200,
        ]);
        $this->setVisibleColumn($this->getConfig('visibleColumn', true));
    }

    /**
     * Added Builder Query
     *
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Eloquent $query
     *
     * @return $this
     */
    public function setQuery($query)
    {
        $this->_query = $query;
        $this->pagination->setQuery($this->_query);

        return $this;
    }

    /**
     * @param array $array
     *
     * @return $this
     */
    public function setFieldsForExport(array $array)
    {
        $this->columns->setFields($array);

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
     * @return Input
     */
    public function input()
    {
        $input = new Input();
        $this->inputs->setInput($input);

        return $input;
    }

    /**
     * @param Button[]|\Closure $action
     *
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
     *
     * @return mixed
     */
    public function render($data = [], $path = 'gridView', $mergeData = [])
    {
        if (request()->ajax() || request()->wantsJson()) {
            $path = $path === 'gridView' ? 'part.grid' : $path;

            return static::view($path, $data, $mergeData)->render();
        }

        return static::view($path, $data, $mergeData)->render();
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     *
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
     *
     * @return \Symfony\Component\Translation\TranslatorInterface|string
     */
    public static function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        return trans(self::NAME . '::' . $id, $parameters, $domain, $locale);
    }

    /**
     * Return get result
     *
     * @return \Assurrussa\GridView\Helpers\GridViewResult
     * @throws ColumnsException
     * @throws QueryException
     */
    public function get()
    {
        $this->fetch();

        $gridViewResult = new \Assurrussa\GridView\Helpers\GridViewResult();
        $gridViewResult->id = $this->getId();
        $gridViewResult->data = $this->pagination->get($this->page, $this->limit);
        $gridViewResult->pagination = $this->getPaginationRender();
        $gridViewResult->headers = $this->columns->toArray();
        $gridViewResult->buttonCreate = $this->buttons->getButtonCreate();
        $gridViewResult->buttonExport = $this->buttons->getButtonExport();
        $gridViewResult->buttonCustoms = $this->buttons->render();
        $gridViewResult->inputCustoms = $this->inputs->render();
        $gridViewResult->filter = $this->_request->toArray();
        $gridViewResult->page = $this->page;
        $gridViewResult->orderBy = $this->orderBy;
        $gridViewResult->search = $this->search;
        $gridViewResult->limit = $this->limit;
        $gridViewResult->sortName = $this->sortName;
        $gridViewResult->counts = $this->counts;
        $gridViewResult->searchInput = $this->searchInput;

        return $gridViewResult;
    }

    /**
     * @param string $id
     *
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
        if (!$this->id) {
            $this->setId(self::NAME . '_' . 1);
        }

        return $this->id;
    }

    /**
     * @param boolean $visibleColumn
     *
     * @return GridView
     */
    public function setVisibleColumn(bool $visibleColumn)
    {
        $this->visibleColumn = $visibleColumn;

        return $this;
    }

    /**
     * @param bool $searchInput
     *
     * @return $this
     */
    public function setSearchInput(bool $searchInput = false)
    {
        $this->searchInput = $searchInput;

        return $this;
    }

    /**
     * @return GridView
     */
    public function setOrderByDesc()
    {
        $this->orderByDefault = Column::FILTER_ORDER_BY_DESC;

        return $this;
    }

    /**
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderByDefault;
    }

    /**
     * @return string
     */
    public function getSortName(): string
    {
        return $this->sortNameDefault;
    }

    /**
     * @param string $sortNameDefault
     *
     * @return $this
     */
    public function setSortName(string $sortNameDefault)
    {
        $this->sortNameDefault = $sortNameDefault;

        return $this;
    }

    /**
     * @param bool $export
     *
     * @return GridView
     */
    public function setExport(bool $export)
    {
        $this->export = $export;

        return $this;
    }

    /**
     * @return bool
     */
    public function isExport(): bool
    {
        return $this->export;
    }

    /**
     * @return boolean
     */
    public function isVisibleColumn(): bool
    {
        return $this->visibleColumn;
    }

    /**
     * @return bool
     */
    public function isSearchInput(): bool
    {
        return $this->searchInput;
    }

    /**
     * @param string $key
     * @param null   $default
     *
     * @return mixed|null
     */
    public function getConfig($key, $default = null)
    {
        if (isset($this->_config[$key])) {
            return $this->_config[$key];
        }

        return $default;
    }

    /**
     * @return $this|bool
     * @throws ColumnsException
     * @throws QueryException
     */
    protected function fetch()
    {
        if (!$this->_query) {
            throw new QueryException();
        }
        $this->_setRequest()
            ->_hasColumns();

        $this->pagination->setColumns($this->columns);

        if (!$this->columns->count()) {
            throw new ColumnsException();
        }

        $this->page = (int)$this->_request->pull('page', 1);
        $this->orderBy = $this->_request->pull('by', $this->getOrderBy());
        $this->search = $this->_request->pull('search', '');
        $this->limit = (int)$this->_request->pull('count', 10);
        $this->sortName = $this->_request->pull('sort', $this->getSortName());
        $export = (bool)$this->_request->pull('export', false);

        $this->filterScopes();
        if ($this->isSearchInput()) {
            $this->filterSearch($this->search);
        }
        $this->filterOrderBy($this->sortName, $this->orderBy);

        if ($this->isExport() && $export) {
            return $this->getExport();
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function getExport()
    {
        return (new ExportData())->fetch($this->_query, $this->columns->toFields());
    }

    /**
     * @return string
     */
    protected function getPaginationRender()
    {
        return $this->pagination->render($this->getConfig('pagination'));
    }

    /**
     * @return $this
     */
    protected function _setRequest()
    {
        if (!$this->_request) {
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
        if (count($this->_request) > 0) {
            foreach ($this->_request as $scope => $value) {
                if (!empty($value)) {
                    //checked scope method for model
                    if (method_exists($this->_query->getModel(), 'scope' . camel_case($scope))) {
                        $this->_query->{camel_case($scope)}($value);
                    } else {
                        $values = explode(',', $value);
                        if (count($values) > 1) {
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
        if ($search) {
            $value = trim($value);
            $search = trim($search);
            /** @var Model $model */
            $model = $this->_query->getModel();
            // поиск по словам
            if (is_string($search) || is_numeric($search)) {
                $this->_query->where(function ($query) use (
                    $model,
                    $search,
                    $value,
                    $delimiter,
                    $beforeValue,
                    $afterValue
                ) {
                    /** @var \Illuminate\Database\Eloquent\Builder $query */
                    $tableName = $model->getTable();
                    if ($value) {
                        if (Model::hasColumn($model, $search)) {
                            $query->orWhere($tableName . '.' . $search, $delimiter,
                                $beforeValue . $value . $afterValue);
                        }
                    } else {
                        foreach (\Schema::getColumnListing($tableName) as $column) {
                            if ($this->hasFilterExecuteForCyrillicColumn($search, $column)) {
                                continue;
                            }
                            if (Model::hasColumn($model, $column)) {
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
        if ($sortName) {
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
        if (!preg_match("/[\w]+/i",
                $search) && \Assurrussa\GridView\Enums\FilterEnum::hasFilterExecuteForCyrillicColumn($column)
        ) {
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
        if (!$this->columns->count()) {
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
        if ($this->isVisibleColumn()) {
            $lists = array_diff($lists, $model->getHidden());
        }
        foreach ($lists as $key => $list) {
            $this->column()
                ->setKey($list)
                ->setValue($list)
                ->setDateActive(true)
                ->setSort(true);
        }
        $this->columnActions(function ($data) {
            $pathNameForModel = strtolower(str_plural(camel_case(class_basename($data))));
            $buttons = [];
            $buttons[] = $this->columnAction()->setActionDelete('amigrid.delete', [$pathNameForModel, $data->id]);
            $buttons[] = $this->columnAction()->setActionShow('amigrid.show',
                [$pathNameForModel, $data->id])->setHandler(function ($data) {
                return false;
            });
            $buttons[] = $this->columnAction()->setActionEdit('amigrid.edit', [$pathNameForModel, $data->id]);

            return $buttons;
        });
        $this->button()->setButtonCreate();
    }
}