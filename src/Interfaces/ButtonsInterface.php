<?php

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface ButtonsInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface ButtonsInterface
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