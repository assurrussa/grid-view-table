<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface InputsInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface InputsInterface
{
    public function setInput(InputInterface $input): InputsInterface;

    public function getInputs(): array;

    public function count(): int;

    public function toArray(): array;

    public function render(): array;
}