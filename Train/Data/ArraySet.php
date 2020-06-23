<?php

declare(strict_types=1);

namespace NeuralNet\Train\Data;

/**
 * Данные для обучения из массива
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
class ArraySet extends Base
{

    /**
     * @var array Массив данных
     */
    protected $data = [];

    /**
     * @var array Текущий один набор данных
     */
    protected $value;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function rewind(): void
    {
        $this->value = \reset($this->data);
    }

    public function valid(): bool
    {
        return $this->value !== false;
    }

    public function current(): array
    {
        return $this->value;
    }

    public function key(): int
    {
        return \key($this->data);
    }

    public function next(): void
    {
        $this->value = \next($this->data);
    }

    public function count(): int
    {
        return \count($this->data);
    }

    public function shuffle(): void
    {
        \shuffle($this->data);
    }

}
