<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Interfaces;

/**
 * Interface ButtonsInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface ButtonsInterface
{
    public function setButton(\Assurrussa\GridView\Interfaces\ButtonInterface $button): ButtonsInterface;

    public function getButtons(): array;

    public function count(): int;

    public function getButtonCreate(): ?string;

    public function getButtonCreateToArray(): array;

    public function getButtonExport(): ?string;

    public function getButtonExportToArray(): array;

    public function toArray(): array;

    public function render(): array;
}