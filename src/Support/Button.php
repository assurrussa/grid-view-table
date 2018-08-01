<?php

declare(strict_types=1);

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
 * @property string $confirmColorOk
 * @property string $confirmColorCancel
 * @property string $confirmTextOk
 * @property string $confirmTextCancel
 * @property array  $options
 * @property array  $strings
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
    const TYPE_ACTION_CUSTOM = 'custom';
    const TYPE_ACTION_CREATE = 'create';
    const TYPE_ACTION_EXPORT = 'export';

    const TYPE_LINK = 'link';
    const TYPE_FORM = 'form';

    /**
     * @var string
     */
    public $type = self::TYPE_LINK;

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
    protected $confirmColorOk = 'btn-primary';
    protected $confirmColorCancel = 'btn-default';
    protected $confirmTextOk = 'ok';
    protected $confirmTextCancel = 'cancel';
    /**
     * @var array
     */
    protected $options = [];
    /**
     * @var array
     */
    protected $strings = [];
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
     * @param string|null $route
     * @param array       $params
     * @param string      $label
     * @param string      $title
     * @param string      $class
     * @param string      $icon
     *
     * @return ButtonInterface
     */
    public function setActionDelete(
        string $route = null,
        array $params = [],
        string $label = '',
        string $title = 'Deleted',
        string $class = 'btn btn-danger btn-sm flat',
        string $icon = 'fa fa-times'
    ): ButtonInterface {
        $this->setAction(self::TYPE_ACTION_DELETE)
            ->setTypeForm()
            ->setLabel($label)
            ->setTitle($title)
            ->setRoute($route, $params)
            ->setClass($class)
            ->setIcon($icon);

        return $this;
    }

    /**
     * @param string|null $route
     * @param array       $params
     * @param string      $label
     * @param string      $title
     * @param string      $class
     * @param string      $icon
     *
     * @return ButtonInterface
     */
    public function setActionRestore(
        string $route = null,
        array $params = [],
        string $label = '',
        string $title = 'Restore',
        string $class = 'btn btn-primary btn-sm flat',
        string $icon = 'fa fa-reply'
    ): ButtonInterface {
        $this->setAction(self::TYPE_ACTION_RESTORE)
            ->setTypeForm()
            ->setRoute($route, $params)
            ->setLabel($label)
            ->setTitle($title)
            ->setClass($class)
            ->setIcon($icon)
//            ->setMethod('PUT')
        ;

        return $this;
    }

    /**
     * @param string|null $route
     * @param array       $params
     * @param string      $label
     * @param string      $title
     * @param string      $class
     * @param string      $icon
     *
     * @return ButtonInterface
     */
    public function setActionEdit(
        string $route = null,
        array $params = [],
        string $label = '',
        string $title = 'Edit',
        string $class = '',
        string $icon = 'fa fa-pencil'
    ): ButtonInterface {
        $this->setAction(self::TYPE_ACTION_EDIT)
            ->setRoute($route, $params)
            ->setLabel($label)
            ->setTitle($title)
            ->setClass($class)
            ->setIcon($icon);

        return $this;
    }

    /**
     * @param string|null $route
     * @param array       $params
     * @param string      $label
     * @param string      $title
     * @param string      $class
     * @param string      $icon
     *
     * @return ButtonInterface
     */
    public function setActionShow(
        string $route = null,
        array $params = [],
        string $label = '',
        string $title = 'Show',
        string $class = '',
        string $icon = 'fa fa-eye'
    ): ButtonInterface {
        $this->setAction(self::TYPE_ACTION_SHOW)
            ->setRoute($route, $params)
            ->setLabel($label)
            ->setTitle($title)
            ->setClass($class)
            ->setIcon($icon);

        return $this;
    }

    /**
     * @param string|null $url
     * @param string      $label
     * @param string      $class
     * @param string      $icon
     *
     * @return ButtonInterface
     */
    public function setActionCustom(
        string $url = null,
        string $label = '',
        string $class = 'btn btn-primary btn-outline-primary btn-sm flat',
        string $icon = 'fa fa-paw'
    ): ButtonInterface {
        $this->setAction(self::TYPE_ACTION_CUSTOM)
            ->setUrl($url)
            ->setLabel($label)
            ->setClass($class)
            ->setIcon($icon);

        return $this;
    }

    /**
     * @param string|null $url
     *
     * @return ButtonInterface
     */
    public function setButtonExport(string $url = null): ButtonInterface
    {
        $addUrl = 'export=1';
        if (!$url) {
            $url = url()->current();
        }
        if ($array = request()->except('export')) {
            $url .= '?' . http_build_query($array);
        }
        $addUrl = str_is('*?*', $url) ? '&' . $addUrl : '?' . $addUrl;
        $text = GridView::trans('grid.export');

        return $this->setActionCustom($url . $addUrl, $text, 'btn btn-default btn-outline-primary', 'fa fa-download')
            ->setAction(self::TYPE_ACTION_EXPORT)
            ->setId('js_amiExportButton');
    }

    /**
     * @param string $url
     *
     * @return ButtonInterface
     */
    public function setButtonCreate(string $url): ButtonInterface
    {
        $text = GridView::trans('grid.create');

        return $this->setActionCustom($url, $text, 'btn btn-primary btn-outline-primary', 'fa fa-plus')
            ->setAction(self::TYPE_ACTION_CREATE);
    }

    /**
     * @param string|null $url
     * @param string|null $addPostUrl
     * @param string|null $view
     * @param string|null $text
     * @param string|null $confirmText
     * @param string|null $class
     * @param string|null $icon
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function setButtonCheckboxAction(
        string $url = null,
        string $addPostUrl = null,
        string $text = null,
        string $confirmText = null,
        string $class = null,
        string $icon = ''
    ): ButtonInterface {
        $addPostUrl = $addPostUrl ?: '?deleted=';
        $class = $class ?: 'btn btn-default btn-outline-primary js_btnCustomAction js_linkDelete';
        $text = $text ?: GridView::trans('grid.selectDelete');
        $confirmText = $confirmText ?: GridView::trans('grid.clickDelete');

        return $this->setActionCustom($url . $addPostUrl, $text, $class, $icon)
            ->setAction(self::TYPE_ACTION_CUSTOM)
            ->setConfirmText($confirmText)
            ->setOptions([
                'data-href'    => $this->getUrl(),
                'data-confirm' => $confirmText,
            ]);
    }

    /**
     * @param bool $var
     *
     * @return ButtonInterface
     */
    public function setVisible(bool $var = true): ButtonInterface
    {
        $this->visibility = $var;

        return $this;
    }

    /**
     * @param array $array
     *
     * @return ButtonInterface
     */
    public function setOptions(array $array = []): ButtonInterface
    {
        $this->options = $array;

        return $this;
    }

    /**
     * @param string $method
     *
     * @return ButtonInterface
     */
    public function setMethod(string $method = 'POST'): ButtonInterface
    {
        if ($this->isVisibility()) {
            $this->method = $method;
        }

        return $this;
    }

    /**
     * @param string $action
     *
     * @return ButtonInterface
     */
    public function setAction(string $action = ''): ButtonInterface
    {
        if ($this->isVisibility()) {
            $this->action = $action;
        }

        return $this;
    }

    /**
     * @param string|null $label
     *
     * @return ButtonInterface
     */
    public function setLabel(string $label = null): ButtonInterface
    {
        if ($this->isVisibility()) {
            $this->label = $label;
        }

        return $this;
    }

    /**
     * @param string|null $icon
     *
     * @return ButtonInterface
     */
    public function setIcon(string $icon = null): ButtonInterface
    {
        if ($this->isVisibility()) {
            $this->icon = $icon;
        }

        return $this;
    }

    /**
     * @param string|null $text
     *
     * @return ButtonInterface
     */
    public function setTitle(string $text = null): ButtonInterface
    {
        if ($this->isVisibility()) {
            $this->title = $text;
        }

        return $this;
    }

    /**
     * @param string|null $id
     *
     * @return ButtonInterface
     */
    public function setId(string $id = null): ButtonInterface
    {
        if ($this->isVisibility()) {
            $this->id = $id;
        }

        return $this;
    }

    /**
     * @param string|null $class
     *
     * @return ButtonInterface
     */
    public function setClass(string $class = null): ButtonInterface
    {
        if ($this->isVisibility()) {
            $this->class = $class;
        }

        return $this;
    }

    /**
     * @param string|null $class
     *
     * @return ButtonInterface
     */
    public function setJsClass(string $class = null): ButtonInterface
    {
        if ($this->isVisibility()) {
            $this->jsClass = $class;
        }

        return $this;
    }

    /**
     * @param string $string
     *
     * @return ButtonInterface
     */
    public function setAddString(string $string): ButtonInterface
    {
        if ($this->isVisibility()) {
            $this->strings[] = $string;
        }

        return $this;
    }

    /**
     * @param string|null $text
     * @param string|null $colorOk
     * @param string|null $colorCancel
     * @param string|null $textOk
     * @param string|null $textCancel
     *
     * @return ButtonInterface
     */
    public function setConfirmText(
        string $text = null,
        string $colorOk = null,
        string $colorCancel = null,
        string $textOk = null,
        string $textCancel = null
    ): ButtonInterface {
        if ($this->isVisibility()) {
            if ($text) {
                $this->confirmText = $text;
            }
            if ($colorOk) {
                $this->confirmColorOk = $colorOk;
            }
            if ($colorCancel) {
                $this->confirmColorCancel = $colorCancel;
            }
            if ($textOk) {
                $this->confirmTextOk = $textOk;
            }
            if ($textCancel) {
                $this->confirmTextCancel = $textCancel;
            }
        }

        return $this;
    }

    /**
     * @param string $url
     *
     * @return ButtonInterface
     */
    public function setUrl(string $url = '#'): ButtonInterface
    {
        if ($this->isVisibility()) {
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
     * @param string|null $route
     * @param array       $params
     *
     * @return ButtonInterface
     */
    public function setRoute(string $route = null, array $params = []): ButtonInterface
    {
        if ($this->isVisibility()) {
            if ($route === null) {
                $url = '#';
            } else {
                $url = '#';
                if (app('router')->has($route)) {
                    $url = route($route, $params);
                } else {
                    $this->setVisible(false);
                }
            }
            $this->setUrl($url);
        }

        return $this;
    }

    /**
     * @param string $type
     *
     * @return ButtonInterface
     */
    public function setType(string $type): ButtonInterface
    {
        if ($this->isVisibility()) {
            $this->type = $type;
        }

        return $this;
    }

    /**
     * @return ButtonInterface
     */
    public function setTypeLink(): ButtonInterface
    {
        return $this->setType(self::TYPE_LINK);
    }

    /**
     * @return ButtonInterface
     */
    public function setTypeForm(): ButtonInterface
    {
        return $this->setType(self::TYPE_FORM);
    }

    /**
     * @param callable $handler
     *
     * @return ButtonInterface
     */
    public function setHandler(Callable $handler): ButtonInterface
    {
        $this->_handler = $handler;

        return $this;
    }

    /**
     * @return bool
     */
    public function getHandler(): bool
    {
        if (is_callable($this->_handler) && $this->getInstance()) {
            return (bool)call_user_func($this->_handler, $this->getInstance());
        }

        return true;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return is_array($this->options) ? $this->options : [];
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model|null $instance
     *
     * @return bool
     */
    public function getValues(\Illuminate\Database\Eloquent\Model $instance = null): bool
    {
        $this->_instance = $instance;
        if ($this->isHandler()) {
            return $this->getHandler();
        }

        return true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getInstance(): ?\Illuminate\Database\Eloquent\Model
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
     * @return null|string
     */
    public function getConfirmText(): ?string
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
     * @return array
     */
    public function getStrings(): array
    {
        return $this->strings;
    }

    /**
     * @return bool
     */
    public function isHandler(): bool
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
    public function toArray(): array
    {
        if (!$this->isVisibility()) {
            return [];
        }

        return [
            'type'               => $this->getType(),
            'action'             => $this->getAction(),
            'method'             => $this->getMethod(),
            'icon'               => $this->getIcon(),
            'label'              => $this->getLabel(),
            'title'              => $this->getTitle(),
            'url'                => $this->getUrl(),
            'id'                 => $this->getId(),
            'class'              => $this->getClass(),
            'jsClass'            => $this->getJsClass(),
            'confirmText'        => $this->getConfirmText(),
            'confirmColorOk'     => $this->confirmColorOk,
            'confirmColorCancel' => $this->confirmColorCancel,
            'confirmTextOk'      => $this->confirmTextOk,
            'confirmTextCancel'  => $this->confirmTextCancel,
            'options'            => $this->getOptions(),
            'strings'            => $this->getStrings(),
        ];
    }

    /**
     * @param string     $view
     * @param array|null $params
     *
     * @return Renderable
     */
    public function render(string $view = null, array $params = null): \Illuminate\Contracts\Support\Renderable
    {
        $view = $view ? $view : 'column.treeControl';
        $params = $params ? $params : $this->toArray();

        return GridView::view($view, $params);
    }


    /**
     * @return string
     */
    public function __toString(): string
    {
        if ($this->isVisibility()) {
            return (string)$this->render();
        }

        return '';
    }
}