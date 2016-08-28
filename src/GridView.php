<?php

namespace Assurrussa\GridView;

use Assurrussa\GridView\Enums\FilterEnum;
use Assurrussa\GridView\Interfaces\GridColumnsInterface;
use Assurrussa\GridView\Interfaces\GridInterface;
use Assurrussa\GridView\Models\Model;
use Assurrussa\GridView\Support\GridColumn;
use Assurrussa\GridView\Support\GridColumns;
use Request;

/**
 * Class GridView
 *
 * @package Assurrussa\GridView
 */
class GridView implements GridInterface
{

    const NAME = 'amigridview';

    /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|static */
    private $_query;

    /** @var GridColumns */
    protected $columns;

    /** @var \Illuminate\Contracts\View\Factory|\Illuminate\View\View */
    private $_createButton = null;

    /** @var \Illuminate\Contracts\View\Factory[]|\Illuminate\View\View[] */
    private $_customButtons = [];

    /** @var \Illuminate\Support\Collection $_request */
    private $_request;

    /**
     * Добавление нужного Builder`а модели
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
     * Получение GridColumns
     *
     * @param \Closure $callback
     * @return GridColumns
     */
    public function grid($callback)
    {
        return new GridColumns($callback);
    }

    /**
     * @param string $path
     * @param array  $data
     * @param array  $mergeData
     * @return mixed
     */
    public function render($path = 'gridView', $data = [], $mergeData = [])
    {
        return $this->view($path, $data, $mergeData);
    }

    /**
     * Добавление необходимых полей для Grid
     *
     * @see GridViewTest::testColumns
     * @param \Closure|GridColumnsInterface $callback
     * @return $this
     */
    public function columns($callback)
    {
        if(is_callable($callback)) {
            $this->columns = call_user_func($callback, new GridColumns());
        } elseif($callback instanceof GridColumnsInterface) {
            $this->columns = $callback;
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function setRequest()
    {
        if(!$this->_request) {
            $this->_request = collect(Request::all());
        }
        return $this;
    }

    /**
     * Получение view кнопки "Новая запись"
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getCreateButton()
    {
        if(is_null($this->_createButton)) {
            $this->setCreateButton();
        }
        return $this->_createButton;
    }

    /**
     * Создание view кнопки "Новая запись"
     *
     * @param \Illuminate\Contracts\View\Factory|\Illuminate\View\View|bool $createButton
     * @return $this
     */
    public function setCreateButton($createButton = true)
    {
        if($createButton === true) {
            $button = $this->view('column.createButton',
                ['url' => $this->_query->getModel()->getTable() . '/create']);
        } elseif($createButton === false) {
            $button = $this->view('column.createButton', []);
        } else {
            $button = $createButton;
        }
        $this->_createButton = $button;
        return $this;
    }

    /**
     * Получение своих кнопок
     *
     * @return string
     */
    public function getCustomButtons()
    {
        if(count($this->_customButtons) == 0) {
            $this->setCustomButtons();
        }
        $buttons = '';
        foreach($this->_customButtons as $customButton) {
            $buttons .= $customButton->render();
        }
        return $buttons;
    }

    /**
     * Создание своих кнопок
     *
     * @param \Illuminate\Contracts\View\Factory[]|\Illuminate\View\View[]|array $customButtons
     * @return $this
     */
    public function setCustomButtons($customButtons = [])
    {
        if(is_array($customButtons) && count($customButtons)) {
            $this->_customButtons = $customButtons;
        } else {
            $this->_customButtons[] = $this->view('column.customButton', []);
        }
        return $this;
    }

    /**
     * Get the evaluated view contents for the given view.
     *
     * @param  string $view
     * @param  array  $data
     * @param  array  $mergeData
     * @return \Illuminate\View\View|\Illuminate\Contracts\View\Factory
     */
    public function view($view = null, $data = [], $mergeData = [])
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
    public function trans($id = null, $parameters = [], $domain = 'messages', $locale = null)
    {
        return trans(self::NAME . '::' . $id, $parameters, $domain, $locale);
    }

    /**
     * Получение массива перебранных данных
     *
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    public function get()
    {
        $this->setRequest();
        $this->_hasColumns();

        $search = $this->_request->pull('search', '');
        $page = (int)$this->_request->pull('page', 1);
        $limit = (int)$this->_request->pull('count', 10);
        $sortName = $this->_request->pull('sortName', 'id');
        $orderBy = $this->_request->pull('orderBy', 'ASC');

        $filters = $this->filterScopes();
        $this->filterSearch($search);
        $this->filterOrderBy($sortName, $orderBy);
        $data = $this->pagination($page, $limit);
        /** @var \Illuminate\Support\HtmlString $pagination */
        //        $pagination = $data->links(self::NAME . '::pagination.grid-pagination');

        return [
            'data'         => $data,
            'pagination'   => $this->getPaginationToArray($data),
            'headers'      => $this->columns->toArray(),
            'createButton' => $this->getCreateButton()->render(),
            'customButton' => $this->getCustomButtons(),
            'page'         => $page,
            'orderBy'      => $orderBy,
            'search'       => $search,
            'count'        => $limit,
            'sortName'     => $sortName,
            'filter'       => $filters->toArray(),
        ];
    }

    /**
     * Возвращает коллекцию в виде пагинации
     *
     * @param int $page
     * @param int $limit
     */
    public function pagination($page, $limit = 10)
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
            $buttons = $this->filterAction($instance);
            if(count($buttons)) {
                $_listRow = array_merge($_listRow, [GridColumn::ACTION_NAME => implode('', $buttons)]);
            }
            $data->offsetSet($key, $_listRow);
        }
        return new \Illuminate\Pagination\LengthAwarePaginator($data, $countTotal, $limit, $page, [
            'path'     => \Illuminate\Pagination\Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);
    }

    /**
     * @param \Illuminate\Pagination\LengthAwarePaginator $data
     * @return array
     */
    public function getPaginationToArray($data)
    {
        $list = [];
        if($data->lastPage() > 1) {
            $window = \Illuminate\Pagination\UrlWindow::make($data);

            $elements = [
                $window['first'],
                is_array($window['slider']) ? '...' : null,
                $window['slider'],
                is_array($window['last']) ? '...' : null,
                $window['last'],
            ];

            if($url = $data->previousPageUrl() ?: '') {
                $list[] = $this->getItemForPagination('', '&laquo;', $url, 'prev', $data->currentPage() - 1);
            };
            foreach($elements as $item) {
                if(is_string($item)) {
                    $list[] = $this->getItemForPagination('disabled', $item);
                }
                if(is_array($item)) {
                    foreach($item as $page => $url) {
                        if($page == $data->currentPage()) {
                            $list[] = $this->getItemForPagination('active', $page);
                        } else {
                            $list[] = $this->getItemForPagination('', '', $url, '', $page);
                        }
                    }
                }
            }
            if($data->hasMorePages()) {
                $list[] = $this->getItemForPagination('', '&raquo;', $data->nextPageUrl(), 'next', $data->currentPage() + 1);
            };
        }
        return $list;
    }

    /**
     * Метод получает кнопки для грид таблицы
     *
     * @param Model|static $instance
     * return array
     */
    protected function filterAction($instance)
    {
        $listButtons = [];
        if($this->columns->count()) {
            $buttons = $this->columns->getActions();
            foreach($buttons as &$button) {
                if($button->getValues($instance)) {
                    $listButtons[] = $button->render();
                    unset($button);
                }
            }
        }
        return $listButtons;
    }

    /**
     * Очень простая фильтрация скоупов.
     *
     * Если необходимо отфильтровать скоупы по id чему либо, обязательно нужно создать scope следующего вида.<br>
     * Пример:
     * * Ссылка для фильтрации - `catalog_id=*`
     * * Метод - `public function scopeCatalogId($int) {}`
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
     * Метод фильтрует данные по словам
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
                    if($value) {
                        if($model->hasColumn($search)) {
                            $query->orWhere($tableName . '.' . $search, $delimiter,
                                $beforeValue . $value . $afterValue);
                        }
                    } else {
                        foreach(\Schema::getColumnListing($tableName) as $column) {
                            // из-за проблем поиска с кириллицей, костыль.
                            if(!preg_match("/[\w]+/i", $search)) {
                                if(FilterEnum::hasFilterExecuteForCyrillicColumn($column)) {
                                    continue;
                                }
                            }
                            if($model->hasColumn($column)) {
                                $query->orWhere($tableName . '.' . $column, 'like', '%' . $search . '%');
                            }
                        }
                    }
                });
            }
        }
    }

    /**
     * Метод указывает сортировку данных
     *
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
     * @param string $status
     * @param string $text
     * @param string $url
     * @param string $rel
     * @param string $page
     * @return array
     */
    protected function getItemForPagination($status = '', $text = '', $url = '', $rel = '', $page = '')
    {
        return [
            'status' => $status,
            'text'   => $text,
            'url'    => $url,
            'rel'    => $rel,
            'page'   => $page,
        ];
    }

    /**
     * Проверка существует ли предварительная подготовка данных.
     */
    private function _hasColumns()
    {
        if(!$this->columns) {
            $this->_prepareColumns();
        }
    }

    /**
     * Метод фильтрует строки по умолчанию для любой модели
     */
    protected function _prepareColumns()
    {
        $this->columns(function ($grid) {
            /** @var  \Assurrussa\GridView\Support\GridColumns $grid */
            $model = $this->_query->getModel();
            $lists = \Schema::getColumnListing($model->getTable());
            $columns = [];
            foreach($lists as $key => $list) {
                $columns[] = $grid->column()->setKey($list)->setValue($list)
                    ->setDate(true)->setSort(true);
            }
            $columns[] = $grid->column()->setKeyAction()->setActions(function ($data) use ($grid) {
                $pathNameForModel = strtolower(str_plural(camel_case(class_basename($data))));
                $buttons = [];
                $buttons[] = $grid->button()
                    ->setAction('delete')
                    ->setLabel('deleted')
                    ->setRoute('delete', [$pathNameForModel, $data->id])
                    ->setIcon('fa-cancel');
                $buttons[] = $grid->button()
                    ->setAction('show')
                    ->setLabel('show')
                    ->setRoute('show', [$pathNameForModel, $data->id])
                    ->setIcon('fa-show')
                    ->setHandler(function ($data) {
                        return false;
                    });
                $buttons[] = $grid->button()
                    ->setAction('edit')
                    ->setLabel('edit')
                    ->setRoute('edit', [$pathNameForModel, $data->id])
                    ->setIcon('fa-edit');
                return $buttons;
            });
            return $grid->setColumns($columns);
        });
    }
}