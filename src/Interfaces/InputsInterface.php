<?php

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface InputsInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface InputsInterface
{
    /**
     * @return int
     */
    public function count();

    /**
     * @return array
     */
    public function toArray();

    /**
     * @return mixed
     */
    public function render();
}