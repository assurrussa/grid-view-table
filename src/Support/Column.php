<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\Interfaces\ColumnInterface;

/**
 * Class Column
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
     * Default format to use for __toString method when type juggling occurs.
     *
     * @var string
     */
    const DEFAULT_TO_STRING_FORMAT = 'Y-m-d H:i:s';

    /**
     * @var string
     */
    private $_key = '';

    /**
     * @var string
     */
    private $_value = '';

    /**
     * @var bool
     */
    private $_sort = false;

    /**
     * @var bool
     */
    private $_screening = false;

    /**
     * @var array
     */
    private $_filter = [];

    /**
     * Format Date
     *
     * @var string
     */
    private $_dateFormat = self::DEFAULT_TO_STRING_FORMAT;

    /**
     * Active date column
     *
     * @var bool
     */
    private $_dateActive = false;

    /**
     * @var \Eloquent|null
     */
    private $_instance = null;

    /**
     * @var Button[]|\Closure|null
     */
    private $_actions = null;

    /**
     * @var \Closure
     */
    private $_handler = null;

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
     * @return $this
     */
    public function setKey($key)
    {
        $this->_key = $key;
        return $this;
    }

    /**
     * Метод для отдельной колонки с действиями
     *
     * @return $this
     */
    public function setKeyAction()
    {
        $this->_key = self::ACTION_NAME;
        $this->setValue(self::ACTION_NAME);
        $this->setSort(false);
        $this->setScreening(true);
        return $this;
    }

    /**
     * Текст для поля
     *
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->_value = $value;
        return $this;
    }

    /**
     * Включить сортировку по колонке
     *
     * @param bool $sort
     * @return $this
     */
    public function setSort($sort = false)
    {
        $this->_sort = $sort;
        return $this;
    }

    /**
     * Разрешить полю выводить html как есть.
     * Внимание!!! опасайтесь XSS атак
     *
     * @param bool $screening
     * @return $this
     */
    public function setScreening($screening = false)
    {
        $this->_screening = $screening;
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
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->_handler = $handler;
        return $this;
    }

    /**
     * Filter for select
     *
     * @param string                               $field Имя поля которое будет фильтроваться
     * @param array|\Illuminate\Support\Collection $array Массив вида [1 => 'name', ...], для селекта
     * @param string                               $mode  Режим
     * @return $this
     */
    public function setFilter($field, $array, $mode = '')
    {
        if($array instanceof \Illuminate\Support\Collection) {
            $array = $array->toArray();
        }
        $this->_filter = [
            'name' => $field,
            'data' => $array,
            'mode' => $mode,
        ];
        return $this;
    }

    /**
     * @param string $field
     * @param string $string
     * @return Column
     */
    public function setFilterString($field, $string = '')
    {
        return $this->setFilter($field, $string, 'string');
    }

    /**
     * @param string $field
     * @param string $string
     * @param bool   $active
     * @param string $format
     * @return Column
     */
    public function setFilterDate($field, $string = '', $active = true, $format = null)
    {
        $this->setDateActive($active);
        if($format) {
            $this->setDateFormat($format);
        }
        return $this->setFilter($field, $string, 'date');
    }

    /**
     * @param $format
     * @return $this
     */
    public function setDateFormat($format)
    {
        $this->_dateFormat = $format;
        return $this;
    }

    /**
     * @param bool $date
     * @return $this
     */
    public function setDateActive($bool = false)
    {
        $this->_dateActive = $bool;
        return $this;
    }

    /**
     * @param Button[]|\Closure $action
     * @return Column
     */
    public function setActions($action)
    {
        $this->setKeyAction();
        $this->_actions = $action;
        return $this;
    }

    /**
     * @return string
     */
    public function isKeyAction()
    {
        return $this->_key == self::ACTION_NAME;
    }

    /**
     * @return bool
     */
    public function isSort()
    {
        return $this->_sort;
    }

    /**
     * @return bool
     */
    public function isScreening()
    {
        return $this->_screening;
    }

    /**
     * Проверяет является ли свойство Closure
     *
     * @return bool
     * @see \Closure
     */
    public function isHandler()
    {
        return is_callable($this->_handler);
    }

    /**
     * @return \Eloquent|null
     */
    public function getInstance()
    {
        return $this->_instance;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->_key;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
    }

    /**
     * @return mixed|null
     */
    public function getHandler()
    {
        if(is_callable($this->_handler) && $this->getInstance()) {
            return call_user_func($this->_handler, $this->getInstance());
        }
        return null;
    }

    /**
     * Взависимости от того какое поле пришло и присутствует ли Closure
     *
     * @param object|null $instance
     * @return bool|mixed
     * @see \Closure
     */
    public function getValues($instance = null)
    {
        if($instance) {
            $this->setInstance($instance);
        }
        if($this->isHandler()) {
            return $this->getHandler();
        }
        return $this->getValueColumn($this->_instance, $this->_key);
    }

    /**
     * Get column value from instance
     *
     * @param \Illuminate\Database\Eloquent\Collection|\Eloquent $instance
     * @param string                                             $name
     * @return mixed
     */
    public function getValueColumn($instance, $name)
    {
        if(!$instance) {
            return null;
        }
        $parts = explode('.', $name);
        $part = array_shift($parts);

        if($instance instanceof \Illuminate\Database\Eloquent\Collection) {
            $instance = $instance->pluck($part)->first();
        } else {
            $instance = $instance->{$part};
        }
        if(!empty($parts) && !is_null($instance)) {
            return $this->getValueColumn($instance, implode('.', $parts));
        }

        if($instance instanceof \DateTimeInterface) {
            $instance = $this->getDateActive() ? $instance->format($this->getDateFormat()) : $instance;
        }
        return $instance;
    }

    /**
     * @return array
     */
    public function getDateFormat()
    {
        return $this->_dateFormat;
    }

    /**
     * @return string
     */
    public function getDateActive()
    {
        return $this->_dateActive;
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * @return Button[]|null
     */
    public function getActions()
    {
        if(is_callable($this->_actions) && $this->getInstance()) {
            return call_user_func($this->_actions, $this->getInstance());
        }
        return $this->_actions;
    }

    /**
     * Необходимо устанавливать только в момент когда приходит инстанс модели!
     *
     * @param \Eloquent|null $instance
     */
    public function setInstance($instance)
    {
        $this->_instance = $instance;
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
                return '<input type="checkbox" class="js-adminCheckboxRow" value="' . $data->id . '">';
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
            'sortBool'        => $this->isSort(),
            'screening'       => $this->isScreening(),
            'filter'          => $this->getFilter(),
            'handler'         => $this->getHandler(),
            'date'            => $this->getDateActive(),
            'format'          => $this->getDateFormat(),
            self::ACTION_NAME => $this->getActions(),
        ];
    }
}