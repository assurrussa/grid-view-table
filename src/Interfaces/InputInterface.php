<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Interfaces;


/**
 * Interface InputInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface InputInterface
{
    /**
     * @param mixed|null $text
     *
     * @return InputInterface
     */
    public function setValue($text = null): InputInterface;

    /**
     * @return mixed
     */
    public function getValue();

    /**
     * @return mixed
     */
    public function getHandler();

    public function setOptions(array $array = []): InputInterface;

    public function setName(string $text = null): InputInterface;

    public function setId(string $id = null): InputInterface;

    public function setClass(string $class = null): InputInterface;

    public function setType(string $type = self::TYPE_HIDDEN): InputInterface;

    public function setTypeText(): InputInterface;

    public function setTypeDate(): InputInterface;

    public function setTypeNumeric(): InputInterface;

    public function setTypeEmail(): InputInterface;

    public function setInputHidden(
        string $name = '',
        string $value = '',
        string $id = '',
        string $class = '',
        array $options = []
    ): InputInterface;

    /**
     * If you need your handler for each column field.
     *
     * @param callable $handler
     *
     * @return InputInterface
     */
    public function setHandler(Callable $handler): InputInterface;

    public function getType(): string;

    public function getOptions(): array;

    public function getId(): string;

    public function getClass(): string;

    public function getName(): string;

    public function isHandler(): bool;

    public function toArray(): array;

    public function render(): \Illuminate\Contracts\Support\Renderable;

    public function __toString(): string;
}