<?php

declare(strict_types=1);

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
     * @param InputInterface $input
     *
     * @return InputsInterface
     */
    public function setInput(InputInterface $input): InputsInterface
    {
        $this->_inputs[] = $input;

        return $this;
    }

    /**
     * @return array|Input[]
     */
    public function getInputs(): array
    {
        return $this->_inputs;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $inputs = [];
        foreach ($this->_inputs as $input) {
            $inputs[] = $input->toArray();
        }

        return $inputs;
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->_inputs);
    }

    /**
     * @return array
     */
    public function render(): array
    {
        $buttons = [];
        foreach ($this->_inputs as $button) {
            $buttons[] = $button->render();
        }

        return $buttons;
    }
}