<?php

declare(strict_types=1);

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
    /** @var \Assurrussa\GridView\Interfaces\ButtonInterface[] */
    private $_buttons = [];

    /**
     * @param ButtonInterface $button
     *
     * @return ButtonsInterface
     */
    public function setButton(\Assurrussa\GridView\Interfaces\ButtonInterface $button): ButtonsInterface
    {
        $this->_buttons[] = $button;

        return $this;
    }

    /**
     * @return array|\Assurrussa\GridView\Interfaces\ButtonInterface[]
     */
    public function getButtons(): array
    {
        return $this->_buttons;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->_buttons);
    }

    /**
     * @return null|string
     */
    public function getButtonCreate(): ?string
    {
        foreach ($this->_buttons as $key => $button) {
            if ($button->getType() == Button::TYPE_BUTTON_CREATE) {
                unset($this->_buttons[$key]);

                return $button->render()->render();
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getButtonCreateToArray(): array
    {
        foreach ($this->_buttons as $key => $button) {
            if ($button->getType() == Button::TYPE_BUTTON_CREATE) {
                unset($this->_buttons[$key]);

                return $button->toArray();
            }
        }

        return [];
    }

    /**
     * @return null|string
     */
    public function getButtonExport(): ?string
    {
        foreach ($this->_buttons as $key => $button) {
            if ($button->getType() == Button::TYPE_BUTTON_EXPORT) {
                unset($this->_buttons[$key]);

                return $button->render()->render();
            }
        }

        return null;
    }

    /**
     * @return array
     */
    public function getButtonExportToArray(): array
    {
        foreach ($this->_buttons as $key => $button) {
            if ($button->getType() == Button::TYPE_BUTTON_EXPORT) {
                unset($this->_buttons[$key]);

                return $button->toArray();
            }
        }

        return [];
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $buttons = [];
        foreach ($this->_buttons as $button) {
            $buttons[] = $button->toArray();
        }

        return $buttons;
    }

    /**
     * @return array
     */
    public function render(): array
    {
        $buttons = [];
        foreach ($this->_buttons as $button) {
            $buttons[] = $button->render();
        }

        return $buttons;
    }
}