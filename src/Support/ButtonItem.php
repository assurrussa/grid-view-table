<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\GridView;

/**
 * Class Button
 *
 * @package Assurrussa\GridView\Support
 */
class ButtonItem extends Button
{
    const TYPE_BUTTON_DEFAULT = 'default';
    const TYPE_BUTTON_CREATE = 'create';
    const TYPE_BUTTON_EXPORT = 'export';

    /** @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|static */
    private $_query;

    public function __construct()
    {
        $this->setTypeDefault();
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|static $query
     * @return ButtonItem
     */
    public function setQuery(&$query)
    {
        $this->_query = $query;
        return $this;
    }

    /**
     * @param mixed $type
     */
    public function setTypeCreate()
    {
        $this->setType(self::TYPE_BUTTON_CREATE);
        return $this;
    }

    /**
     * @param mixed $type
     */
    public function setTypeExport()
    {
        $this->setType(self::TYPE_BUTTON_EXPORT);
        return $this;
    }

    /**
     * @param mixed $type
     */
    public function setTypeDefault()
    {
        $this->setType(self::TYPE_BUTTON_DEFAULT);
        return $this;
    }

    /**
     * @param \Illuminate\Contracts\View\Factory|\Illuminate\View\View|bool $button
     * @return bool|string
     */
    public function setButtonCreate($button = true)
    {
        return $this->setTypeCreate()
            ->setClass('btn btn-primary')
            ->setTitle(GridView::trans('grid.create'))
            ->setLabel(GridView::trans('grid.create'))
            ->setIcon('fa fa-plus')
            ->setButtonCustom($button, '/create');
    }

    /**
     * @param \Illuminate\Contracts\View\Factory|\Illuminate\View\View|bool $button
     * @return bool|string
     */
    public function setButtonExport($button = true)
    {
        return $this->setTypeExport()
            ->setClass('btn btn-default')
            ->setTitle(GridView::trans('grid.export'))
            ->setLabel(GridView::trans('grid.export'))
            ->setIcon('fa fa-upload')
            ->setButtonCustom($button, '/export');
    }

    /**
     * Создание view кнопки "Новая запись"
     *
     * Example:
     * * 1) $this->setButtonCreate(true)
     * * 2) $this->setButtonCreate(false)
     * * 3) $this->setButtonCreate(view('column.createButton', ['url' => route('entity.create')]))
     * * 4) $this->setButtonCreate(route('entity.create'))
     *
     * @param \Illuminate\Contracts\View\Factory|\Illuminate\View\View|bool $buttonView
     * @param string                                                        $postfix
     * @return bool|string
     */
    public function setButtonCustom($buttonView = true, $postfix = '')
    {
        if($buttonView === true) {
            if($this->_query) {
                $button = $this->setUrl($this->_query->getModel()->getTable() . $postfix)->renderGridView();
            } else {
                $button = $this->setUrl($buttonView)->renderGridView();
            }
        } elseif($buttonView === false) {
            $button = $this->renderGridView();
        } elseif(is_string($buttonView)) {
            $button = $this->setUrl($buttonView)->renderGridView();
        } else {
            $button = $buttonView;
        }
        return $button;
    }

    /**
     * Кастомная кнопка для удаления
     *
     * @param string      $route
     * @param string|null $params
     * @param string|null $text
     * @param string|null $confirmText
     * @param string|null $class
     * @param string|null $icon
     * @return string
     */
    public function setButtonCheckboxAction(
        $route,
        $params = null,
        $view = null,
        $text = null,
        $confirmText = null,
        $class = null,
        $icon = null
    ) {
        $route = $route ?: '';
        $params = $params ?: ['deleted='];
        $class = $class ?: 'btn btn-default js-btnCustomAction js-linkDelete';
        $icon = $icon ?: '';
        $text = $text ?: GridView::trans('grid.selectDelete');
        $confirmText = $confirmText ?: GridView::trans('grid.clickDelete');
        $this->setRoute($route, $params)
            ->setClass($class)
            ->setIcon($icon)
            ->setLabel($text)
            ->setConfirmText($confirmText)
            ->setOptions([
                'data-href'            => $this->getUrl(),
                'data-confirm-deleted' => $confirmText,
            ]);
        return $this->renderGridView($view);
    }

    /**
     * @param string|null $view
     * @param array       $params
     * @return string
     */
    protected function renderGridView($view = null, $params = [])
    {
        $view = $view ? $view : 'column.treeControl';
        $view = GridView::NAME . '::' . $view;
        return $this->render($view, $params);
    }

    /**
     * @param string|null $view
     * @param array       $params
     * @return string
     */
    public function render($view = null, $params = [])
    {
        $view = $view ?: GridView::NAME . '::' . 'column.treeControl';
        $params = count($params) ? $params : $this->toArray();
        return (string)view($view, $params);
    }
}