<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\GridView;
use Assurrussa\GridView\Interfaces\InputInterface;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\View;

/**
 * Class ButtonItem
 *
 * @property string $type
 * @property string $method
 * @property string $id
 * @property string $class
 * @property string $name
 * @property string $value
 * @property array  $options
 *
 * @package Assurrussa\AmiCMS\Support
 */
class Input implements InputInterface, Renderable, Arrayable
{
    const TYPE_HIDDEN = 'hidden';
    const TYPE_TEXT = 'text';
    const TYPE_DATE = 'date';
    const TYPE_EMAIL = 'email';
    const TYPE_NUMERIC = 'numeric';

    /**
     * @var string
     */
    public $type = self::TYPE_HIDDEN;

    /**
     * @var string
     */
    protected $id = '';
    /**
     * @var string
     */
    protected $class = '';
    /**
     * @var string
     */
    protected $name = '';
    /**
     * @var string
     */
    protected $value = '';
    /**
     * @var array
     */
    protected $options = [];
    /**
     * @var \Closure
     */
    private $_handler = null;

    /**
     * @param bool $var
     * @return $this
     */
    public function setOptions(array $array = [])
    {
        $this->options = $array;
        return $this;
    }

    /**
     * @param string $class
     * @return static
     */
    public function setName($text = null)
    {
        $this->name = $text;
        return $this;
    }

    /**
     * @param string $class
     * @return static
     */
    public function setValue($text = null)
    {
        $this->value = $text;
        return $this;
    }

    /**
     * @param string $class
     * @return static
     */
    public function setId($id = null)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @param string $class
     * @return static
     */
    public function setClass($class = null)
    {
        $this->class = $class;
        return $this;
    }

    /**
     * @param mixed $type
     */
    public function setType($type = self::TYPE_HIDDEN)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @param mixed $type
     */
    public function setTypeText()
    {
        return $this->setType(self::TYPE_TEXT);
    }

    /**
     * @param mixed $type
     */
    public function setTypeDate()
    {
        return $this->setType(self::TYPE_DATE);
    }

    /**
     * @param mixed $type
     */
    public function setTypeNumeric()
    {
        return $this->setType(self::TYPE_NUMERIC);
    }

    /**
     * @param mixed $type
     */
    public function setTypeEmail()
    {
        return $this->setType(self::TYPE_EMAIL);
    }


    /**
     * @param null        $route
     * @param array       $params
     * @param string      $label
     * @param string      $title
     * @param string|null $class
     * @param string      $icon
     * @return $this
     */
    public function setInputHidden($name = '', $value = '', $id = '', $class = '', $options = [])
    {
        $this->setType(self::TYPE_HIDDEN)
            ->setName($name)
            ->setValue($value)
            ->setId($id)
            ->setClass($class)
            ->setOptions($options);
        return $this;
    }

    /**
     * Если необходим свой обработчик для каждого поля колонки.
     *
     * @param \Closure $handler  функция замыкания для своего условия
     * @param object   $instance Примается модель которая будет обрабатыватся в Closure
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->_handler = $handler;
        return $this;
    }

    /**
     * @return bool|mixed
     */
    public function getHandler()
    {
        if(is_callable($this->_handler)) {
            return call_user_func($this->_handler);
        }
        return true;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return is_array($this->options) ? $this->options : [];
    }

    /**
     * @return bool|mixed
     */
    public function getValue()
    {
        if($this->isHandler()) {
            return $this->getHandler();
        }
        return $this->value;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return bool
     */
    public function isHandler() : bool
    {
        return is_callable($this->_handler);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type'    => $this->getType(),
            'id'      => $this->getId(),
            'class'   => $this->getClass(),
            'name'    => $this->getName(),
            'value'   => $this->getValue(),
            'options' => $this->getOptions(),
        ];
    }

    /**
     * @return View
     */
    public function render()
    {
        return GridView::view('input.input', $this->toArray());
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->render();
    }
}