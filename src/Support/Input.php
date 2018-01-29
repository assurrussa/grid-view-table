<?php

declare(strict_types=1);

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
     * @param null $text
     *
     * @return InputInterface
     */
    public function setValue($text = null): InputInterface
    {
        $this->value = $text;

        return $this;
    }

    /**
     * @return bool|mixed|string
     */
    public function getValue()
    {
        if ($this->isHandler()) {
            return $this->getHandler();
        }

        return $this->value;
    }

    /**
     * @return bool|mixed
     */
    public function getHandler()
    {
        if (is_callable($this->_handler)) {
            return call_user_func($this->_handler);
        }

        return true;
    }

    /**
     * @param array $array
     *
     * @return InputInterface
     */
    public function setOptions(array $array = []): InputInterface
    {
        $this->options = $array;

        return $this;
    }

    /**
     * @param string|null $text
     *
     * @return InputInterface
     */
    public function setName(string $text = null): InputInterface
    {
        $this->name = $text;

        return $this;
    }

    /**
     * @param string|null $id
     *
     * @return InputInterface
     */
    public function setId(string $id = null): InputInterface
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param string|null $class
     *
     * @return InputInterface
     */
    public function setClass(string $class = null): InputInterface
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param string $type
     *
     * @return InputInterface
     */
    public function setType(string $type = self::TYPE_HIDDEN): InputInterface
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return InputInterface
     */
    public function setTypeText(): InputInterface
    {
        return $this->setType(self::TYPE_TEXT);
    }

    /**
     * @return InputInterface
     */
    public function setTypeDate(): InputInterface
    {
        return $this->setType(self::TYPE_DATE);
    }

    /**
     * @return InputInterface
     */
    public function setTypeNumeric(): InputInterface
    {
        return $this->setType(self::TYPE_NUMERIC);
    }

    /**
     * @return InputInterface
     */
    public function setTypeEmail(): InputInterface
    {
        return $this->setType(self::TYPE_EMAIL);
    }


    /**
     * @param string $name
     * @param string $value
     * @param string $id
     * @param string $class
     * @param array  $options
     *
     * @return InputInterface
     */
    public function setInputHidden(
        string $name = '',
        string $value = '',
        string $id = '',
        string $class = '',
        array $options = []
    ): InputInterface {
        $this->setType(self::TYPE_HIDDEN)
            ->setName($name)
            ->setValue($value)
            ->setId($id)
            ->setClass($class)
            ->setOptions($options);

        return $this;
    }

    /**
     * If you need your handler for each column field.
     *
     * @param callable $handler
     *
     * @return $this
     */
    public function setHandler(Callable $handler): InputInterface
    {
        $this->_handler = $handler;

        return $this;
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
    public function isHandler(): bool
    {
        return is_callable($this->_handler);
    }

    /**
     * @return array
     */
    public function toArray(): array
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
    public function render(): Renderable
    {
        return GridView::view('input.input', $this->toArray());
    }


    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string)$this->render();
    }
}