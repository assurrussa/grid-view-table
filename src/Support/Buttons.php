<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\Interfaces\ButtonInterface;
use Assurrussa\GridView\Interfaces\ButtonsInterface;

/**
 * Class Buttons
 *
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
 * @property bool   $visibility
 *
 * @package Assurrussa\AmiCMS\Support
 */
class Buttons implements ButtonsInterface
{
    /** @var ButtonItem[] */
    private $_buttons = [];

    /**
     * Добавление необходимых полей для Grid
     *
     * @param \Closure|ButtonInterface $buttons
     * @return $this
     */
    public function setButton($button)
    {
        $this->_buttons[] = $button;
        return $this;
    }

    /**
     * Получение необходимых полей для Grid
     *
     * @return Button[]
     */
    public function getButtons()
    {
        return $this->_buttons;
    }

    /**
     * Получение необходимых полей для Grid
     *
     * @return array
     */
    public function toArray()
    {
        $buttons = [];
        foreach($this->_buttons as $button) {
            $buttons[] = $button->toArray();
        }
        return $buttons;
    }

    /**
     * Получение необходимых полей для Grid
     *
     * @return array
     */
    public function render()
    {
        $buttons = [];
        foreach($this->_buttons as $button) {
            $buttons[] = $button->render();
        }
        return $buttons;
    }

    /**
     * Получение количества колонок
     *
     * @return int
     */
    public function count()
    {
        return count($this->_buttons);
    }

    /**
     * Return button "Create +"
     *
     * @param bool $inArray
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|null
     */
    public function getButtonCreate($inArray = false)
    {
        foreach($this->_buttons as $key => $button) {
            if($button->getType() == ButtonItem::TYPE_BUTTON_CREATE) {
                unset($this->_buttons[$key]);
                if($inArray) {
                    return $button->toArray();
                }
                return $button->render();
            }
        }
        return null;
    }

    /**
     * Return button "Export"
     *
     * @param bool $inArray
     * @return array|\Illuminate\Contracts\View\Factory|\Illuminate\View\View|null
     */
    public function getButtonExport($inArray = false)
    {
        foreach($this->_buttons as $key => $button) {
            if($button->getType() == ButtonItem::TYPE_BUTTON_EXPORT) {
                unset($this->_buttons[$key]);
                if($inArray) {
                    return $button->toArray();
                }
                return $button->render();
            }
        }
        return null;
    }
}