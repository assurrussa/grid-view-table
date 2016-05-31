<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\Interfaces\GridColumnInterface;

/**
 * Class GridColumn
 *
 * @package Assurrussa\AmiCMS\Components\Support
 */
class GridColumn implements GridColumnInterface
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
    private $_key;

    /**
     * @var string
     */
    private $_value;

    /**
     * @var bool
     */
    private $_sort = false;

    /**
     * @var bool
     */
    private $_screening = false;

    /**
     * @var \Closure
     */
    private $_handler;

    /**
     * @var array
     */
    private $_filter = [];

    /**
     * Формат даты
     *
     * @var string
     */
    private $_format = self::DEFAULT_TO_STRING_FORMAT;

    /**
     * Формат даты
     *
     * @var string
     */
    private $_date = false;

    /**
     * @var \Eloquent|null
     */
    private $_instance = null;

    /**
     * @var ButtonItem[]|\Closure|null
     */
    private $_actions = null;

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
     * Filter принимает в себя 2 параметра
     *
     * @param string                               $field Имя поля которое будет фильтроваться
     * @param array|\Illuminate\Support\Collection $array Массив вида [1 => 'name', ...], для селекта
     * @return $this
     */
    public function setFilter($field, $array)
    {
        if($array instanceof \Illuminate\Support\Collection) {
            $array = $array->toArray();
        }
        $this->_filter = [
            'name' => $field,
            'data' => $array,
        ];
        return $this;
    }

    /**
     * Необходимо устанавливать только в момент когда приходит инстанс модели!
     *
     * @param \Eloquent|null $instance
     */
    private function _setInstance($instance)
    {
        $this->_instance = $instance;
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
    public function isKeyAction()
    {
        return $this->_key == self::ACTION_NAME;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->_value;
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
     * @return bool
     */
    public function getHandler()
    {
        if(is_callable($this->_handler) && $this->getInstance()) {
            return call_user_func($this->_handler, $this->getInstance());
        }
        return true;
    }

    /**
     * Проверяет является ли свойство Closure
     *
     * @return bool
     * @see \Closure
     */
    public function hasHandler()
    {
        return is_callable($this->_handler);
    }

    /**
     * Взависимости от того какое поле пришло и присутствует ли Closure
     *
     * @param $instance
     * @return bool|mixed
     * @see \Closure
     */
    public function getValues($instance)
    {
        $this->_setInstance($instance);
        if($this->hasHandler()) {
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
            $instance = $this->getDate() ? $instance->format($this->getFormat()) : $instance;
        }
        return $instance;
    }

    /**
     * @return array
     */
    public function setFormat($format)
    {
        $this->_format = $format;
        return $this;
    }

    /**
     * @return array
     */
    public function getFormat()
    {
        return $this->_format;
    }

    /**
     * @param bool $date
     * @return $this
     */
    public function setDate($bool = false)
    {
        $this->_date = $bool;
        return $this;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->_date;
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->_filter;
    }

    /**
     * @param ButtonItem []\Closure $action
     * @return GridColumn
     */
    public function setActions($action)
    {
        $this->_actions = $action;
        return $this;
    }

    /**
     * @return ButtonItem[]|null
     */
    public function getActions()
    {
        if(is_callable($this->_actions) && $this->getInstance()) {
            return call_user_func($this->_actions, $this->getInstance());
        }
        return $this->_actions;
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
            'date'            => $this->getDate(),
            'format'          => $this->getFormat(),
            self::ACTION_NAME => $this->getActions(),
        ];
    }
}