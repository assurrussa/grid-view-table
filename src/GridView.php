<?php

declare(strict_types=1);

namespace Assurrussa\GridView;

use Assurrussa\GridView\Exception\ColumnsException;
use Assurrussa\GridView\Exception\QueryException;
use Assurrussa\GridView\Export\ExportData;
use Assurrussa\GridView\Interfaces\ButtonInterface;
use Assurrussa\GridView\Interfaces\ColumnInterface;
use Assurrussa\GridView\Interfaces\GridInterface;
use Assurrussa\GridView\Models\Model;
use Assurrussa\GridView\Support\Button;
use Assurrussa\GridView\Support\Buttons;
use Assurrussa\GridView\Support\Column;
use Assurrussa\GridView\Support\ColumnCeil;
use Assurrussa\GridView\Support\Columns;
use Assurrussa\GridView\Support\EloquentPagination;
use Assurrussa\GridView\Support\Input;
use Assurrussa\GridView\Support\Inputs;
use Illuminate\Contracts\Support\Renderable;

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
    /** @var string */
    public $sortNameDefault = 'id';
    /** @var int */
    public $defaultCountItems = 10;
    /** @var array */
    public $counts;
    /** @var array */
    public $filter;
    /** @var bool */
    public $export = false;
    /**  @var bool */
    public $visibleColumn;
    /** @var Columns */
    public $columns;
    /** @var Buttons */
    public $buttons;
    /** @var Inputs */
    public $inputs;
    /** @var EloquentPagination */
    public $pagination;
    /** @var string */
    protected $locationUrl = '';
    /** @var array */
    protected $requestParams = [];
    /** @var string */
    protected $formAction = '';
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
        $this->counts = $this->getConfig('limit', [
            10  => 10,
            25  => 25,
            100 => 100,
            200 => 200,
        ]);
        $this->filter = $this->getConfig('filter', [
            'operator'    => 'like',
            'beforeValue' => '',
            'afterValue'  => '%',
        ]);
        $this->setVisibleColumn((bool)$this->getConfig('visibleColumn', true));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|static $query
     *
     * @return GridInterface
     */
    public function setQuery(\Illuminate\Database\Eloquent\Builder $query): GridInterface
    {
        $this->_query = $query;
        $this->pagination->setQuery($this->_query);

        return $this;
    }

    /**
     * example:
     * $fields = [
     *             'ID'            => 'id',
     *             'Time'          => 'setup_at',
     *             0               => 'brand.name',
     *             'Name'          => function() {return 'name';},
     *         ];
     *
     * @param array $fields
     *
     * @return $this
     */
    public function setFieldsForExport(array $array): GridInterface
    {
        $this->columns->setFields($array);

        return $this;
    }

    /**
     * @return Button
     */
    public function button(): Button
    {
        $button = new Button();
        $this->buttons->setButton($button);

        return $button;
    }

    /**
     * @return Column
     */
    public function column(): Column
    {
        $column = new Column();
        $this->columns->setColumn($column);

        return $column;
    }

    /**
     * @return Input
     */
    public function input(): Input
    {
        $input = new Input();
        $this->inputs->setInput($input);

        return $input;
    }

    /**
     * @param callable    $action
     * @param string|null $value
     *
     * @return ColumnInterface
     */
    public function columnActions(Callable $action, string $value = null): ColumnInterface
    {
        return $this->column()->setActions($action, $value);
    }

    /**
     * @return Button
     */
    public function columnAction(): Button
    {
        return new Button();
    }

    /**
     * @return ColumnCeil
     */
    public static function columnCeil(): ColumnCeil
    {
        return new ColumnCeil();
    }

    /**
     * @param array  $data
     * @param string $path
     * @param array  $mergeData
     *
     * @return string
     * @throws \Throwable
     */
    public function render(array $data = [], string $path = 'gridView', array $mergeData = []): string
    {
        if (request()->ajax() || request()->wantsJson()) {
            $path = $path === 'gridView' ? 'part.grid' : $path;

            return json_encode([
                'url'  => $data['data']->location . '?' . http_build_query($data['data']->requestParams),
                'data' => static::view($path, $data, $mergeData)->render(),
            ]);
        }

        return static::view($path, $data, $mergeData)->render();
    }

    /**
     * @param array  $data
     * @param string $path
     * @param array  $mergeData
     *
     * @return string
     * @throws \Throwable
     */
    public function renderFirst(array $data = [], string $path = 'gridView', array $mergeData = []): string
    {
        $path = $path === 'gridView' ? 'part.tableTrItem' : $path;

        $headers = $data['data']->headers;
        $item = (array)$data['data']->data;

        return static::view($path, [
            'headers' => $headers,
            'item'    => $item,
        ], $mergeData)->render();
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
    public static function view(string $view = null, array $data = [], array $mergeData = []): Renderable
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
    public static function trans(string $id = null, array $parameters = [], string $domain = 'messages', string $locale = null): string
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
    public function get(): \Assurrussa\GridView\Helpers\GridViewResult
    {
        $this->fetch();

        $gridViewResult = new \Assurrussa\GridView\Helpers\GridViewResult();
        $gridViewResult->id = $this->getId();
        $gridViewResult->formAction = $this->formAction;
        $gridViewResult->location = $this->locationUrl;
        $gridViewResult->requestParams = $this->requestParams;
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
     * @return \Assurrussa\GridView\Helpers\GridViewResult
     * @throws ColumnsException
     * @throws QueryException
     */
    public function first(): \Assurrussa\GridView\Helpers\GridViewResult
    {
        $this->fetch();

        $_listRow = [];
        if ($instance = $this->_query->first()) {
            foreach ($this->columns->getColumns() as $column) {
                $_listRow[$column->getKey()] = $column->getValues($instance);
            }
            $buttons = $this->columns->filterActions($instance);
            if (count($buttons)) {
                $_listRow = array_merge($_listRow, [Column::ACTION_NAME => implode('', $buttons)]);
            }
        }

        $gridViewResult = new \Assurrussa\GridView\Helpers\GridViewResult();
        $gridViewResult->headers = $this->columns->toArray();
        $gridViewResult->data = $_listRow;

        return $gridViewResult;
    }

    /**
     * @param string $id
     *
     * @return GridView
     */
    public function setId(string $id): GridInterface
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
    public function setVisibleColumn(bool $visibleColumn): GridInterface
    {
        $this->visibleColumn = $visibleColumn;

        return $this;
    }

    /**
     * @param int $defaultCountItems
     *
     * @return GridInterface
     */
    public function setDefaultCountItems(int $defaultCountItems): GridInterface
    {
        $this->defaultCountItems = $defaultCountItems;

        return $this;
    }

    /**
     * @param bool $searchInput
     *
     * @return $this
     */
    public function setSearchInput(bool $searchInput = false): GridInterface
    {
        $this->searchInput = $searchInput;

        return $this;
    }

    /**
     * @return GridView
     */
    public function setOrderByDesc(): GridInterface
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
    public function setSortName(string $sortNameDefault): GridInterface
    {
        $this->sortNameDefault = $sortNameDefault;

        return $this;
    }

    /**
     * @param bool $export
     *
     * @return GridView
     */
    public function setExport(bool $export): GridInterface
    {
        $this->export = $export;

        return $this;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function setFormAction(string $url): GridInterface
    {
        $this->formAction = $url;

        return $this;
    }

    /**
     * @param null $text
     *
     * @return string
     */
    public function getFormAction(): string
    {
        return $this->formAction;
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
    protected function getConfig($key, $default = null)
    {
        if (isset($this->_config[$key])) {
            return $this->_config[$key];
        }

        return $default;
    }

    /**
     * @return GridInterface
     * @throws ColumnsException
     * @throws QueryException
     */
    protected function fetch(): GridInterface
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

        $this->locationUrl = $this->_request->pull('location');
        $this->requestParams = $this->_request->all();
        $this->page = (int)$this->_request->pull('page', 1);
        $this->orderBy = $this->_request->pull('by', $this->getOrderBy());
        $this->search = $this->_request->pull('search', '');
        $this->limit = $this->countItems();
        $this->sortName = $this->_request->pull('sort', $this->getSortName());
        $export = (bool)$this->_request->pull('export', false);

        $this->filterScopes();
        if ($this->isSearchInput()) {
            $this->filterSearch($this->search);
        }
        $this->filterOrderBy($this->sortName, $this->orderBy);

        if ($this->isExport() && $export && $this->getExport()) {
            return $this;
        }

        return $this;
    }

    /**
     * @return bool
     */
    protected function getExport(): bool
    {
        return (new ExportData())->fetch($this->_query, $this->columns->toFields());
    }

    /**
     * @return string
     */
    protected function getPaginationRender(): string
    {
        return $this->pagination->render($this->getConfig('pagination'), $this->requestParams, $this->getFormAction());
    }

    /**
     * @return $this
     */
    protected function _setRequest(): GridInterface
    {
        if (!$this->_request) {
            $this->_request = collect(request()->all());
        }

        return $this;
    }

    /**
     * Very simple filtration scopes.<br><br>
     *
     * Example:
     * * method - `public function scopeCatalogId($int) {}`
     *
     * @return \Illuminate\Support\Collection
     */
    protected function filterScopes(): \Illuminate\Support\Collection
    {
        if (count($this->_request) > 0) {
            foreach ($this->_request as $scope => $value) {
                if (!empty($value) || $value === 0 || $value === '0') {
                    //checked scope method for model
                    if (method_exists($this->_query->getModel(), 'scope' . camel_case($scope))) {
                        $this->_query->{camel_case($scope)}($value);
                    } else {
                        $values = explode(',', (string)$value);
                        if (count($values) > 1) {
                            $this->filterSearch($scope, $values[0], '>');
                            $this->filterSearch($scope, $values[1], '<');
                        } else {
                            $this->filterSearch($scope, $value, $this->filter['operator'], $this->filter['beforeValue'],
                                $this->filter['afterValue']);
                        }
                    }
                }
            }
            $this->_query->addSelect($this->_query->getModel()->getTable() . '.*');
        }

        return $this->_request;
    }

    /**
     * The method filters the data according
     *
     * @param string|int $search
     * @param mixed|null $value       word
     * @param string     $operator    equal sign - '=', 'like' ...
     * @param string     $beforeValue First sign before value
     * @param string     $afterValue  Last sign after value
     */
    protected function filterSearch(
        string $search,
        $value = null,
        string $operator = '=',
        string $beforeValue = '',
        string $afterValue = ''
    ): void {
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
                    $operator,
                    $beforeValue,
                    $afterValue
                ) {
                    /** @var \Illuminate\Database\Eloquent\Builder $query */
                    $tableName = $model->getTable();
                    if ($value) {
                        if (Model::hasColumn($model, $search)) {
                            $query->orWhere($tableName . '.' . $search, $operator,
                                $beforeValue . $value . $afterValue);
                        }
                    } else {
                        foreach (\Schema::getColumnListing($tableName) as $column) {
                            if ($this->hasFilterExecuteForCyrillicColumn($search, $column)) {
                                continue;
                            }
                            if (Model::hasColumn($model, $column)) {
                                $query->orWhere($tableName . '.' . $column, $operator, $beforeValue . $search . $afterValue);
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
    protected function filterOrderBy(string $sortName, string $orderBy): void
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
     *
     * @return bool
     */
    protected function hasFilterExecuteForCyrillicColumn(string $search, string $column): bool
    {
        if (!preg_match("/[\w]+/i", $search) && \Assurrussa\GridView\Enums\FilterEnum::hasFilterExecuteForCyrillicColumn($column)) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    protected function countItems(): int
    {
        $count = $this->_request->has('count')
            ? $this->_request->pull('count')
            : array_first($this->counts);
        if (!isset($this->counts[$count])) {
            $count = $this->defaultCountItems;
        }

        return (int)$count;
    }

    /**
     * Check exists prepared columns<br><br>
     * Проверка существует ли предварительная подготовка данных.
     */
    protected function _hasColumns(): void
    {
        if (!$this->columns->count()) {
            $this->_prepareColumns();
        }
    }

    /**
     * The method takes the default column for any model<br><br>
     * Метод получает колонки по умолчанию для любой модели
     */
    protected function _prepareColumns(): void
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
            $buttons = [];
            if ($this->getConfig('routes')) {
                $pathNameForModel = strtolower(str_plural(camel_case(class_basename($data))));
                $buttons[] = $this->columnAction()->setActionDelete('amigrid.delete', [$pathNameForModel, $data->id]);
                $buttons[] = $this->columnAction()->setActionShow('amigrid.show',
                    [$pathNameForModel, $data->id])->setHandler(function ($data) {
                    return false;
                });
                $buttons[] = $this->columnAction()->setActionEdit('amigrid.edit', [$pathNameForModel, $data->id]);
            }

            return $buttons;
        });
        if ($this->getConfig('routes')) {
            $pathNameForModel = strtolower(str_plural(camel_case(class_basename($model))));
            $this->button()->setButtonCreate(route('amigrid.create', [$pathNameForModel]));
        }
    }
}
