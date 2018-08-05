<?php

declare(strict_types=1);

namespace Assurrussa\GridView\Interfaces;


/**
 * Interface ButtonInterface
 *
 * @package Assurrussa\GridView\Interfaces
 */
interface ButtonInterface
{
    public function setVisible(bool $var = true): ButtonInterface;

    public function setId(string $id = null): ButtonInterface;

    public function setClass(string $class = null): ButtonInterface;

    public function setJsClass(string $class = null): ButtonInterface;

    public function setMethod(string $method = 'POST'): ButtonInterface;

    public function setAction(string $action = ''): ButtonInterface;

    public function setLabel(string $label = null): ButtonInterface;

    public function setIcon(string $icon = null): ButtonInterface;

    public function setTitle(string $text = null): ButtonInterface;

    public function setConfirmText(
        string $text = null,
        string $colorOk = null,
        string $colorCancel = null,
        string $textOk = null,
        string $textCancel = null
    ): ButtonInterface;

    public function setUrl(string $url = '#'): ButtonInterface;

    public function setRoute(string $route = null, array $params = []): ButtonInterface;

    public function setType(string $type): ButtonInterface;

    public function setTypeLink(): ButtonInterface;

    public function setTypeForm(): ButtonInterface;

    public function setOptions(array $array = []): ButtonInterface;

    /**
     * If you need your handler for each column field.
     *
     * @param callable $handler
     *
     * @return ButtonInterface
     */
    public function setHandler(Callable $handler): ButtonInterface;

    /**
     * Custom add string in form. Displaying Unescaped Data.
     * Be very careful when echoing content that is supplied by users of your application.
     * Always use the escaped, double curly brace syntax to prevent XSS attacks when displaying user supplied data.
     *
     * @param string $string
     *
     * @return ButtonInterface
     */
    public function setAddString(string $string): ButtonInterface;

    public function isVisibility(): bool;

    public function isHandler(): bool;

    public function getId(): ?string;

    public function getClass(): ?string;

    public function getJsClass(): ?string;

    public function getMethod(): string;

    public function getAction(): string;

    public function getLabel(): ?string;

    public function getIcon(): ?string;

    public function getTitle(): ?string;

    public function getConfirmText(): ?string;

    public function getUrl(): string;

    public function getType(): string;

    public function getOptions(): array;

    public function getHandler(): bool;

    public function getStrings(): array;

    /**
     * It is necessary to install only when the model instance comes!
     *
     * @param \Illuminate\Database\Eloquent\Model|null $instance
     *
     * @return bool
     */
    public function getValues(\Illuminate\Database\Eloquent\Model $instance = null): bool;

    public function getInstance(): ?\Illuminate\Database\Eloquent\Model;

    public function toArray(): array;

    /**
     * @param string $view
     * @param array  $params
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function render(string $view = 'column.treeControl', array $params = []): \Illuminate\Contracts\Support\Renderable;

    public function __toString(): string;
}