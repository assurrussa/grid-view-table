<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\GridView;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\View\View;

/**
 * Class ButtonItem
 *
 * @property string $action
 * @property string $icon
 * @property string $label
 * @property string $url
 * @property bool   $visibility
 *
 * @package Assurrussa\AmiCMS\Support
 */
class ButtonItem implements Renderable
{
    /**
     * Menu item label
     *
     * @var string
     */
    protected $action;
    /**
     * Menu item label
     *
     * @var string
     */
    protected $label;
    /**
     * Menu item icon
     *
     * @var string
     */
    protected $icon;
    /**
     * Menu item url
     *
     * @var string
     */
    protected $url;
    /**
     * Menu item visibility
     *
     * @var bool
     */
    protected $visibility = true;
    /**
     * @var \Closure
     */
    private $_handler;

    /**
     * @var object|null
     */
    private $_instance = null;

    /**
     * Get or set menu item action
     *
     * @param string|null $action
     * @return $this|string
     */
    public function setAction($action = '')
    {
        if($this->visibility) {
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
        if($this->visibility) {
            $this->label = $label;
        }

        return $this;
    }

    /**
     * Get or set menu item icon
     *
     * @param string|null $icon
     * @return $this|string
     */
    public function setIcon($icon = null)
    {
        if($this->visibility) {
            if(is_null($icon)) {
                return $this->icon;
            }
            $this->icon = $icon;
        }
        return $this;
    }

    /**
     * Get or set menu item url
     *
     * @param string|null $url
     * @return $this|string
     */
    public function setUrl($url = null)
    {
        if($this->visibility) {
            if(is_null($url)) {
                if(!is_null($this->url)) {
                    if(strpos($this->url, '://') !== false) {
                        return $this;
                    }
                    return route('admin.dashboard', $this->url);
                }
                return '#';
            }
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
        if($this->visibility) {
            if(is_null($route)) {
                $route = route('admin.dashboard');
            } else {
                $route = route($route, $params);
            }
            $this->url = $route;
        }
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
     * @param bool $var
     * @return $this
     */
    public function setVisible($var = true)
    {
        if(!$var) {
            $this->visibility = false;
        }
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
     * Взависимости от того какое поле пришло и присутствует ли Closure
     *
     * @param object $instance
     * @return bool|mixed
     */
    public function getValues($instance)
    {
        $this->_setInstance($instance);
        if($this->hasHandler()) {
            return $this->getHandler();
        }
        return true;
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

    /**
     * @return null|object
     */
    public function getInstance()
    {
        return $this->_instance;
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
     * @return View
     */
    public function render()
    {
        $params = [
            'action' => $this->action,
            'icon'   => $this->icon,
            'label'  => $this->label,
            'url'    => $this->url,
        ];
        return view(GridView::NAME . '::column.treeControl', $params);
    }


    /**
     * @return string
     */
    public function __toString()
    {
        if($this->visibility) {
            return (string)$this->render();
        }
        return '';
    }

}