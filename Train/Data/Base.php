<?php

declare(strict_types=1);

namespace NeuralNet\Train\Data;

/**
 * Данные для обучения
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
abstract class Base implements \Iterator, \Countable
{

    /**
     * Перевод итератора в начало
     *
     * @return void
     */
    abstract public function rewind(): void;

    /**
     * Проверка валидности элемента
     *
     * @return bool
     */
    abstract public function valid(): bool;

    /**
     * Один набор для обучения [input, output]
     *
     * @return array
     */
    abstract public function current(): array;

    /**
     * Ключ
     *
     * @return scalar
     */
    abstract public function key(): int;

    /**
     * Перевод итератора на следующий элемент
     *
     * @return void
     */
    abstract public function next(): void;

    /**
     * Получение размера данных
     *
     * @return int
     */
    public function count(): int
    {
        return null;
    }

    /**
     * Перемешивание данных
     *
     * @return void
     */
    public function shuffle(): void
    {

    }

}
