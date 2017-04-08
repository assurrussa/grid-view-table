<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\Interfaces\ColumnInterface;

/**
 * Class Column
 *
 * @property string                 $key
 * @property string                 $value
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
    /** type array for filter */
    const FILTER_TYPE_SELECT = 'select';

    const FILTER_ORDER_BY_ASC = 'asc';
    const FILTER_ORDER_BY_DESC = 'desc';

    const FILTER_KEY_NAME = 'name';
    const FILTER_KEY_DATA = 'data';
    const FILTER_KEY_MODE = 'mode';

    /**
     * @property string
     */
    public $key = '';

    /**
     * @property string
     */
    public $value = '';

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
        $this->setDateFormat(config('amigrid.format'));
    }

    /**
     * Какое поле из таблицы вытащить
     *
     * @param string $key
     *
     * @return $this
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Метод для отдельной колонки с действиями
     *
     * @return $this
     */
    public function setKeyAction()
    {
        $this->key = self::ACTION_NAME;
        $this->setValue(self::ACTION_NAME);
        $this->setSort(false);
        $this->setScreening(true);

        return $this;
    }

    /**
     * Текст для поля
     *
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Включить сортировку по колонке
     *
     * @param bool $sort
     *
     * @return $this
     */
    public function setSort($sort = false)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Разрешить полю выводить html как есть.
     * Внимание!!! опасайтесь XSS атак
     *
     * @param bool $screening
     *
     * @return $this
     */
    public function setScreening($screening = false)
    {
        $this->screening = $screening;

        return $this;
    }

    /**
     * Если необходим свой обработчик для каждого поля колонки.
     * * Пример:
     * *  ->handler(function ($data) {
     * *      ...
     * *  }),
     *
     * @param \Closure $handler функция замыкания для своего условия принимает в себя $_instance модели.
     *
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;

        return $this;
    }

    /**
     * Filter for select
     *
     * @param string                               $field Имя поля которое будет фильтроваться
     * @param array|\Illuminate\Support\Collection $array Массив вида [1 => 'name', ...], для селекта
     * @param string                               $mode  Режим
     *
     * @return $this
     */
    public function setFilter($field, $array, $mode = self::FILTER_TYPE_SELECT)
    {
        if ($array instanceof \Illuminate\Support\Collection) {
            $array = $array->toArray();
        }
        $this->filter = [
            self::FILTER_KEY_NAME => $field,
            self::FILTER_KEY_DATA => $array,
            self::FILTER_KEY_MODE => $mode,
        ];

        return $this;
    }

    /**
     * @param string $field
     * @param string $string
     *
     * @return Column
     */
    public function setFilterString($field, $string = '')
    {
        return $this->setFilter($field, $string, self::FILTER_TYPE_STRING);
    }

    /**
     * @param string $field
     * @param string $string
     * @param bool   $active
     * @param string $format
     *
     * @return Column
     */
    public function setFilterDate($field, $string = '', $active = true, $format = null)
    {
        $this->setDateActive($active);
        if ($format) {
            $this->setDateFormat($format);
        }

        return $this->setFilter($field, $string, self::FILTER_TYPE_DATE);
    }

    /**
     * @param $format
     *
     * @return $this
     */
    public function setDateFormat($format)
    {
        $this->dateFormat = $format;

        return $this;
    }

    /**
     * @param bool $date
     *
     * @return $this
     */
    public function setDateActive($bool = false)
    {
        $this->dateActive = $bool;

        return $this;
    }

    /**
     * @param Button[]|\Closure $action
     *
     * @return Column
     */
    public function setActions($action)
    {
        $this->setKeyAction();
        $this->actions = $action;

        return $this;
    }

    /**
     * Если необходим свой обработчик для каждой строки таблицы.
     * * Пример:
     * *  ->setClass(function ($data) {
     * *      ...
     * *  }),
     *
     * @param \Closure $handler функция замыкания для своего условия принимает в себя $_instance модели.
     *
     * @return $this
     */
    public function setClassForString($handler)
    {
        $this->key = self::ACTION_STRING_TR;
        $this->setHandler($handler);

        return $this;
    }

    /**
     * @return string
     */
    public function isKeyAction()
    {
        return $this->key == self::ACTION_NAME;
    }

    /**
     * @return bool
     */
    public function isSort()
    {
        return $this->sort;
    }

    /**
     * @return bool
     */
    public function isScreening()
    {
        return $this->screening;
    }

    /**
     * Проверяет является ли свойство Closure
     *
     * @return bool
     * @see \Closure
     */
    public function isHandler()
    {
        return is_callable($this->handler);
    }

    /**
     * Проверяет является ли свойство Closure
     *
     * @return bool
     * @see \Closure
     */
    public function isStringTr()
    {
        return $this->key === self::ACTION_STRING_TR;
    }

    /**
     * @return \Eloquent|null
     */
    public function getInstance()
    {
        return $this->instance;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return callable|\Closure|mixed|null
     */
    public function getHandler()
    {
        if (is_callable($this->handler)) {
            if ($this->getInstance()) {
                return call_user_func($this->handler, $this->getInstance());
            }

            return $this->handler;
        }

        return null;
    }

    /**
     * Взависимости от того какое поле пришло и присутствует ли Closure
     *
     * @param object|null $instance
     *
     * @return bool|mixed
     * @see \Closure
     */
    public function getValues($instance = null)
    {
        if ($instance) {
            $this->setInstance($instance);
        }
        if ($this->isHandler()) {
            return $this->getHandler();
        }

        return $this->getValueColumn($this->instance, $this->key);
    }

    /**
     * Get column value from instance
     *
     * @param \Illuminate\Database\Eloquent\Collection|\Eloquent $instance
     * @param string                                             $name
     *
     * @return mixed
     */
    public function getValueColumn($instance, $name)
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
     * @return array
     */
    public function getDateFormat()
    {
        return $this->dateFormat;
    }

    /**
     * @return string
     */
    public function getDateActive()
    {
        return $this->dateActive;
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->filter;
    }

    /**
     * @return Button[]|null
     */
    public function getActions()
    {
        if (is_callable($this->actions) && $this->getInstance()) {
            return call_user_func($this->actions, $this->getInstance());
        }

        return $this->actions;
    }

    /**
     * Необходимо устанавливать только в момент когда приходит инстанс модели!
     *
     * @param \Eloquent|null $instance
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;
    }

    /**
     * шаблон для чекбокса
     *
     * @return $this
     */
    public function setCheckbox()
    {
        return $this->setKey('checkbox')
            ->setValue('<input type="checkbox" class="js-adminSelectAll">')
            ->setSort(false)
            ->setScreening(true)
            ->setHandler(function ($data) {
                if ($data && $data->id) {
                    return '<input type="checkbox" class="js-adminCheckboxRow" value="' . $data->id . '">';
                }

                return '';
            });
    }

    /**
     * @return array
     */
    public function toArray()
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
            self::ACTION_NAME => $this->getActions(),
        ];
    }
}