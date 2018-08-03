<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\Interfaces\ColumnInterface;

/**
 * Class Column
 *
 * @property string                 $key
 * @property string                 $value
 * @property string                 $url
 * @property bool                   $sort
 * @property bool                   $screening
 * @property array                  $filter
 * @property string                 $dateFormat
 * @property bool                   $dateActive
 * @property \Eloquent|null         $instance
 * @property \Closure               $handler
 * @property Button[]|\Closure|null $actions
 *
 * @package Assurrussa\GridView\Support
 */
class Column implements ColumnInterface
{

    /**
     * Кey for Action
     *
     * @var string
     */
    const ACTION_NAME = 'action';

    /**
     * Кey for string columns
     *
     * @var string
     */
    const ACTION_STRING_TR = 'tr';

    /**
     * Default format to use for __toString method when type juggling occurs.
     *
     * @var string
     */
    const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s';

    /** type string for filter */
    const FILTER_TYPE_STRING = 'string';
    /** type date for filter */
    const FILTER_TYPE_DATE = 'date';
    const FILTER_TYPE_DATE_RANGE = 'date_range';
    /** type array for filter */
    const FILTER_TYPE_SELECT = 'select';
    const FILTER_TYPE_SELECT_AJAX = 'select_ajax';
    const FILTER_TYPE_SELECT_NOT_AJAX = 'select_not_ajax';

    const FILTER_ORDER_BY_ASC = 'asc';
    const FILTER_ORDER_BY_DESC = 'desc';

    const FILTER_KEY_NAME = 'name';
    const FILTER_KEY_DATA = 'data';
    const FILTER_KEY_SELECTED = 'selected';
    const FILTER_KEY_MODE = 'mode';
    const FILTER_KEY_CLASS = 'class';
    const FILTER_KEY_STYLE = 'style';
    const FILTER_KEY_PLACEHOLDER = 'placeholder';
    const FILTER_KEY_WIDTH = 'width';
    const FILTER_KEY_FORMAT = 'format';

    /**
     * @property string
     */
    public $key = '';

    /**
     * @property string
     */
    public $value = '';

    /**
     * @property string
     */
    public $url = '';

    /**
     * @property bool
     */
    public $sort = false;

    /**
     * @property bool
     */
    public $screening = false;

    /**
     * @property array
     */
    public $filter = [];

    /**
     * Format Date
     *
     * @property string
     */
    public $dateFormat = self::DEFAULT_TO_STRING_FORMAT;

    /**
     * Active date column
     *
     * @property bool
     */
    public $dateActive = false;

    /**
     * @property \Eloquent|null
     */
    public $instance = null;

    /**
     * @property Button[]|\Closure|null
     */
    public $actions = null;

    /**
     * @property \Closure
     */
    public $handler = null;

    /**
     * Column constructor.
     */
    public function __construct()
    {
        $this->setDateFormat((string)config('amigrid.format', ''));
    }

    /**
     * template for checkbox
     *
     * @return $this
     */
    public function setCheckbox(): ColumnInterface
    {
        return $this->setKey('checkbox')
            ->setValue('<input type="checkbox" class="js_adminSelectAll">')
            ->setSort(false)
            ->setScreening(true)
            ->setHandler(function ($data) {
                if ($data && $data->id) {
                    return '<input type="checkbox" class="js_adminCheckboxRow" value="' . $data->id . '">';
                }

                return '';
            });
    }

    /**
     * Value for the field
     *
     * @param $value
     *
     * @return ColumnInterface
     */
    public function setValue($value): ColumnInterface
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return mixed|null
     */
    public function getHandler()
    {
        if ($this->isHandler() && $this->getInstance()) {
            return call_user_func($this->handler, $this->getInstance());
        }

        return $this->handler;
    }

    /**
     * @param null $instance
     *
     * @return mixed|null
     */
    public function getValues($instance = null)
    {
        if ($instance) {
            $this->setInstance($instance);
        }
        if ($this->isHandler()) {
            return $this->getHandler();
        }

        if ($this->instance) {
            return $this->getValueColumn($this->instance, $this->key);
        }

        return null;
    }

    /**
     * @param mixed  $instance
     * @param string $name
     *
     * @return mixed
     */
    public function getValueColumn($instance, string $name)
    {
        if (!$instance) {
            return null;
        }
        $parts = explode('.', $name);
        $part = array_shift($parts);

        if ($instance instanceof \Illuminate\Database\Eloquent\Collection) {
            $instance = $instance->pluck($part)->first();
        } else {
            $instance = $instance->{$part};
        }
        if (!empty($parts) && !is_null($instance)) {
            return $this->getValueColumn($instance, implode('.', $parts));
        }

        if ($instance instanceof \DateTimeInterface) {
            $instance = $this->getDateActive() ? $instance->format($this->getDateFormat()) : $instance;
        }

        return $instance;
    }

    /**
     * Необходимо устанавливать только в момент когда приходит инстанс модели!
     *
     * @param \Eloquent|null $instance
     */
    public function setInstance(\Illuminate\Database\Eloquent\Model $instance): ColumnInterface
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Which field from the table to pull out
     *
     * @param string $key
     *
     * @return ColumnInterface
     */
    public function setKey(string $key): ColumnInterface
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Method for a single column with actions
     *
     * @return ColumnInterface
     */
    public function setKeyAction(): ColumnInterface
    {
        $this->key = self::ACTION_NAME;
        $this->setValue('');
        $this->setSort(false);
        $this->setScreening(true);

        return $this;
    }

    /**
     * @param bool $sort
     *
     * @return ColumnInterface
     */
    public function setSort(bool $sort = false): ColumnInterface
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * @param bool $screening
     *
     * @return ColumnInterface
     */
    public function setScreening(bool $screening = false): ColumnInterface
    {
        $this->screening = $screening;

        return $this;
    }

    /**
     * @param callable $handler
     *
     * @return ColumnInterface
     */
    public function setHandler(Callable $handler): ColumnInterface
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * @param string       $field
     * @param string|array $data
     * @param string|null  $mode
     * @param array        $selected
     * @param string       $class
     * @param string       $style
     * @param string       $placeholder
     * @param string       $url
     *
     * @return ColumnInterface
     */
    public function setFilter(
        string $field,
        $data,
        string $mode = null,
        array $selected = [],
        string $class = '',
        string $style = '',
        string $placeholder = '',
        string $url = ''
    ): ColumnInterface {
        $mode = $mode ? $mode : self::FILTER_TYPE_SELECT;
        $this->filter = [
            self::FILTER_KEY_NAME        => $field,
            self::FILTER_KEY_DATA        => $data,
            self::FILTER_KEY_MODE        => $mode,
            self::FILTER_KEY_SELECTED    => $selected,
            self::FILTER_KEY_CLASS       => $class,
            self::FILTER_KEY_STYLE       => $style,
            self::FILTER_KEY_PLACEHOLDER => $placeholder,
            self::FILTER_KEY_WIDTH       => '180px',
            self::FILTER_KEY_FORMAT      => 'DD MMM YY',
        ];
        $this->setUrl($url);

        return $this;
    }

    /**
     * @param string $width
     *
     * @return ColumnInterface
     */
    public function setFilterFormat(string $format = 'DD MMM YY'): ColumnInterface
    {
        $this->filter[self::FILTER_KEY_FORMAT] = $format;

        return $this;
    }

    /**
     * @param string $width
     *
     * @return ColumnInterface
     */
    public function setFilterWidth(string $width = '180px'): ColumnInterface
    {
        $this->filter[self::FILTER_KEY_WIDTH] = $width;

        return $this;
    }

    /**
     * @param string $field
     * @param array  $array
     * @param string $class
     * @param string $style
     * @param string $placeholder
     *
     * @return ColumnInterface
     */
    public function setFilterSelect(
        string $field,
        array $array,
        string $class = '',
        string $style = '',
        string $placeholder = ''
    ): ColumnInterface {
        return $this->setFilter($field, $array, self::FILTER_TYPE_SELECT, [], $class, $style, $placeholder);
    }

    /**
     * @param string $field
     * @param array  $array
     * @param string $url
     * @param string $class
     * @param string $style
     * @param string $placeholder
     *
     * @return ColumnInterface
     */
    public function setFilterSelectAjax(
        string $field,
        array $array,
        array $selected,
        string $url,
        string $class = '',
        string $style = '',
        string $placeholder = ''
    ): ColumnInterface {
        return $this->setFilter($field, $array, self::FILTER_TYPE_SELECT_AJAX, $selected, $class, $style, $placeholder, $url);
    }

    /**
     * @param string $field
     * @param array  $array
     * @param string $url
     * @param string $class
     * @param string $style
     * @param string $placeholder
     *
     * @return ColumnInterface
     */
    public function setFilterSelectNotAjax(
        string $field,
        array $array,
        array $selected = [],
        string $url = '',
        string $class = '',
        string $style = '',
        string $placeholder = ''
    ): ColumnInterface {
        return $this->setFilter($field, $array, self::FILTER_TYPE_SELECT_NOT_AJAX, $selected, $class, $style, $placeholder, $url);
    }

    /**
     * @param string $field
     * @param string $string
     * @param string $class
     * @param string $style
     * @param string $placeholder
     *
     * @return ColumnInterface
     */
    public function setFilterString(
        string $field,
        string $string = '',
        string $class = '',
        string $style = '',
        string $placeholder = ''
    ): ColumnInterface {
        return $this->setFilter($field, $string, self::FILTER_TYPE_STRING, [], $class, $style, $placeholder);
    }

    /**
     * @param string      $field
     * @param string      $string
     * @param bool        $active
     * @param string|null $format
     * @param string      $class
     * @param string      $style
     * @param string      $placeholder
     *
     * @return ColumnInterface
     */
    public function setFilterDate(
        string $field,
        string $string = '',
        bool $active = true,
        string $format = null,
        string $class = '',
        string $style = '',
        string $placeholder = ''
    ): ColumnInterface {
        $this->setDateActive($active);
        if ($format) {
            $this->setDateFormat($format);
        }

        return $this->setFilter($field, $string, self::FILTER_TYPE_DATE, [], $class, $style, $placeholder);
    }

    /**
     * @param string      $field
     * @param string      $string
     * @param bool        $active
     * @param string|null $format
     * @param string      $class
     * @param string      $style
     * @param string      $placeholder
     *
     * @return ColumnInterface
     */
    public function setFilterDateRange(
        string $field,
        string $string = '',
        bool $active = true,
        string $format = null,
        string $class = '',
        string $style = '',
        string $placeholder = ''
    ): ColumnInterface {
        $this->setDateActive($active);
        if ($format) {
            $this->setDateFormat($format);
        }

        return $this->setFilter($field, $string, self::FILTER_TYPE_DATE_RANGE, [], $class, $style, $placeholder);
    }

    /**
     * @param string $format
     *
     * @return ColumnInterface
     */
    public function setDateFormat(string $format): ColumnInterface
    {
        $this->dateFormat = $format;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return ColumnInterface
     */
    public function setDateActive(bool $bool = false): ColumnInterface
    {
        $this->dateActive = $bool;

        return $this;
    }

    /**
     * @param callable    $action
     * @param string|null $value
     *
     * @return ColumnInterface
     */
    public function setActions(Callable $action, string $value = null): ColumnInterface
    {
        $this->setKeyAction();
        $this->actions = $action;
        if ($value !== null) {
            $this->setValue($value);
        }

        return $this;
    }

    /**
     * @param callable $handler
     *
     * @return ColumnInterface
     */
    public function setClassForString(Callable $handler): ColumnInterface
    {
        $this->key = self::ACTION_STRING_TR;
        $this->setHandler($handler);

        return $this;
    }

    /**
     * @param callable $handler
     *
     * @return ColumnInterface
     */
    public function setUrl(string $url): ColumnInterface
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return bool
     */
    public function isKeyAction(): bool
    {
        return $this->key == self::ACTION_NAME;
    }

    /**
     * @return bool
     */
    public function isSort(): bool
    {
        return $this->sort;
    }

    /**
     * @return bool
     */
    public function isScreening(): bool
    {
        return $this->screening;
    }

    /**
     * @return bool
     */
    public function isHandler(): bool
    {
        return is_callable($this->handler);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getInstance(): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->instance;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function getDateActive(): bool
    {
        return $this->dateActive;
    }

    /**
     * @return array
     */
    public function getFilter(): array
    {
        return $this->filter;
    }

    /**
     * @return Button[]|null
     */
    public function getActions()
    {
        if (is_callable($this->actions)) {
            if ($this->getInstance()) {
                return call_user_func($this->actions, $this->getInstance());
            }
        }

        return $this->actions;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'key'             => $this->getKey(),
            'value'           => $this->getValue(),
            'sort'            => $this->isSort(),
            'screening'       => $this->isScreening(),
            'filter'          => $this->getFilter(),
            'handler'         => $this->getHandler(),
            'date'            => $this->getDateActive(),
            'format'          => $this->getDateFormat(),
            'url'             => $this->getUrl(),
            self::ACTION_NAME => $this->getActions(),
        ];
    }
}
