<?php

namespace Assurrussa\GridView\Support;

use Assurrussa\GridView\Interfaces\InputInterface;
use Assurrussa\GridView\Interfaces\InputsInterface;


/**
 * Class Inputs
 *
 * @package Assurrussa\GridView\Support
 */
class Inputs implements InputsInterface
{
    /** @var Input[] */
    private $_inputs = [];

    /**
     * Добавление необходимых полей для Grid
     *
     * @param \Closure|InputInterface $inputs
     * @return $this
     */
    public function setInput($input)
    {
        $this->_inputs[] = $input;
        return $this;
    }

    /**
     * @return Input[]
     */
    public function getInputs()
    {
        return $this->_inputs;
    }

    /**
     * Получение необходимых полей для Grid
     *
     * @return array
     */
    public function toArray()
    {
        $inputs = [];
        foreach($this->_inputs as $input) {
            $inputs[] = $input->toArray();
        }
        return $inputs;
    }

    /**
     * Получение количества колонок
     *
     * @return int
     */
    public function count()
    {
        return count($this->_inputs);
    }

    /**
     * @return array
     */
    public function render()
    {
        $buttons = [];
        foreach($this->_inputs as $button) {
            $buttons[] = $button->render();
        }
        return $buttons;
    }
}