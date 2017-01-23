<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\GridView;
use Assurrussa\GridView\Interfaces\ButtonInterface;
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
 * @property string $jsClass
 * @property string $action
 * @property string $icon
 * @property string $label
 * @property string $title
 * @property string $url
 * @property string $confirmText
 * @property array  $options
 * @property bool   $visibility
 *
 * @package Assurrussa\AmiCMS\Support
 */
class Button implements ButtonInterface, Renderable, Arrayable
{
    const TYPE_ACTION_SHOW = 'show';
    const TYPE_ACTION_DELETE = 'delete';
    const TYPE_ACTION_EDIT = 'edit';
    const TYPE_ACTION_RESTORE = 'restore';

    /**
     * @var string
     */
    public $type;

    /**
     * @var string
     */
    protected $method = 'POST';
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
    protected $jsClass = '';
    /**
     * @var string
     */
    protected $action = '';
    /**
     * @var string
     */
    protected $label = '';
    /**
     * @var string
     */
    protected $title = '';
    /**
     * @var string
     */
    protected $icon = '';
    /**
     * @var string
     */
    protected $url = '';
    /**
     * @var string
     */
    protected $confirmText = '';
    /**
     * @var array
     */
    protected $options = [];
    /**
     * Menu item visibility
     *
     * @var bool
     */
    protected $visibility = true;
    /**
     * @var \Closure
     */
    private $_handler = null;

    /**
     * @var object|null
     */
    private $_instance = null;

    /**
     * @param bool $var
     * @return $this
     */
    public function setVisible(bool $var = true)
    {
        $this->visibility = $var;
        return $this;
    }

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
     * Get or set menu item action
     *
     * @param string|null $action
     * @return $this|string
     */
    public function setMethod($method = 'POST')
    {
        if($this->isVisibility()) {
            $this->method = $method;
        }

        return $this;
    }

    /**
     * Get or set menu item action
     *
     * @param string|null $action
     * @return $this|string
     */
    public function setAction($action = '')
    {
        if($this->isVisibility()) {
            $this->action = $action;
        }

        return $this;
    }

    /**
     * Get or set menu item label
     *
     * @param string|null $label
     * @return $this|string
     */
    public function setLabel($label = null)
    {
        if($this->isVisibility()) {
            $this->label = $label;
        }

        return $this;
    }

    /**
     * Get or set menu item icon
     *
     * @param string|null $icon
     * @return $this
     */
    public function setIcon($icon = null)
    {
        if($this->isVisibility()) {
            $this->icon = $icon;
        }
        return $this;
    }

    /**
     * @param string $class
     * @return static
     */
    public function setTitle($text = null)
    {
        if($this->isVisibility()) {
            $this->title = $text;
        }
        return $this;
    }

    /**
     * @param string $class
     * @return static
     */
    public function setId($id = null)
    {
        if($this->isVisibility()) {
            $this->id = $id;
        }
        return $this;
    }

    /**
     * @param string $class
     * @return static
     */
    public function setClass($class = null)
    {
        if($this->isVisibility()) {
            $this->class = $class;
        }
        return $this;
    }

    /**
     * @param string $class
     * @return static
     */
    public function setJsClass($class = null)
    {
        if($this->isVisibility()) {
            $this->jsClass = $class;
        }
        return $this;
    }

    /**
     * @param string $class
     * @return static
     */
    public function setConfirmText($text = null)
    {
        if($this->isVisibility()) {
            $this->confirmText = $text;
        }
        return $this;
    }

    /**
     * Get or set menu item url
     *
     * @param string|null $url
     * @return $this
     */
    public function setUrl($url = '#')
    {
        if($this->isVisibility()) {
            //            if($this->url !== null) {
            //                if(strpos($this->url, '://') !== false) {
            //                    return $this;
            //                }
            //            }
            $this->url = $url;
        }
        return $this;
    }

    /**
     * Get or set menu item route
     *
     * @param string|null $route
     * @return $this|string
     */
    public function setRoute($route = null, $params = [])
    {
        if($this->isVisibility()) {
            if($route === null) {
                $url = '#';
            } else {
                $url = route($route, $params);
            }
            $this->setUrl($url);
        }
        return $this;
    }

    /**
     * @param mixed $type
     */
    public function setType($type)
    {
        if($this->isVisibility()) {
            $this->type = $type;
        }
        return $this;
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
    public function setActionDelete(
        $route = null,
        $params = [],
        $label = '',
        $title = 'Deleted',
        $class = 'btn btn-danger btn-sm flat',
        $icon = 'fa fa-times'
    ) {
        $this->setAction(self::TYPE_ACTION_DELETE)
            ->setLabel($label)
            ->setTitle($title)
            ->setRoute($route, $params)
            ->setClass($class)
            ->setIcon($icon);
        return $this;
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
    public function setActionRestore(
        $route = null,
        $params = [],
        $label = '',
        $title = 'Restore',
        $class = 'btn btn-primary btn-sm flat',
        $icon = 'fa fa-reply'
    ) {
        $this->setAction(self::TYPE_ACTION_RESTORE)
            ->setRoute($route, $params)
            ->setLabel($label)
            ->setTitle($title)
            ->setClass($class)
            ->setIcon($icon);
        return $this;
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
    public function setActionEdit($route = null, $params = [], $label = '', $title = 'Edit', $class = '', $icon = 'fa fa-pencil')
    {
        $this->setAction(self::TYPE_ACTION_EDIT)
            ->setRoute($route, $params)
            ->setLabel($label)
            ->setTitle($title)
            ->setClass($class)
            ->setIcon($icon);
        return $this;
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
    public function setActionShow($route = null, $params = [], $label = '', $title = 'Show', $class = '', $icon = 'fa fa-eye')
    {
        $this->setAction(self::TYPE_ACTION_SHOW)
            ->setRoute($route, $params)
            ->setLabel($label)
            ->setTitle($title)
            ->setClass($class)
            ->setIcon($icon);
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
     * Взависимости от того какое поле пришло и присутствует ли Closure
     *
     * @param object $instance
     * @return bool|mixed
     */
    public function getValues($instance)
    {
        $this->_setInstance($instance);
        if($this->isHandler()) {
            return $this->getHandler();
        }
        return true;
    }

    /**
     * @return null|object
     */
    public function getInstance()
    {
        return $this->_instance;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getConfirmText(): string
    {
        return $this->confirmText;
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
    public function getJsClass(): string
    {
        return $this->jsClass;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return !empty($this->title) ? $this->title : $this->getLabel();
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
    public function isHandler() : bool
    {
        return is_callable($this->_handler);
    }

    /**
     * @return bool
     */
    public function isVisibility(): bool
    {
        return $this->visibility;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'type'        => $this->getType(),
            'action'      => $this->getAction(),
            'icon'        => $this->getIcon(),
            'label'       => $this->getLabel(),
            'title'       => $this->getTitle(),
            'url'         => $this->getUrl(),
            'id'          => $this->getId(),
            'class'       => $this->getClass(),
            'jsClass'     => $this->getJsClass(),
            'confirmText' => $this->getConfirmText(),
            'options'     => $this->getOptions(),
        ];
    }

    /**
     * @return View
     */
    public function render()
    {
        return GridView::view('column.treeControl', $this->toArray());
    }


    /**
     * @return string
     */
    public function __toString()
    {
        if($this->isVisibility()) {
            return (string)$this->render();
        }
        return '';
    }

    /**
     * Необходимо устанавливать только в момент когда приходит инстанс модели!
     *
     * @param null|object $instance
     */
    private function _setInstance($instance)
    {
        $this->_instance = $instance;
    }
}