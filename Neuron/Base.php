<?php

declare(strict_types=1);

namespace NeuralNet\Neuron;

/**
 * Базовый нейрон
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2016 Gordon Freeman
 */
abstract class Base
{

    /**
     * @var array Входные данные
     */
    public $input = [];

    /**
     * @var float Смещение
     */
    public $bias = 0;

    /**
     * @var array Веса
     */
    public $weight = [];

    /**
     * @var array Выход
     */
    public $output;

    /**
     * Конструктор нейрона
     *
     * @param int $count Кол-во входов нейрона
     */
    public function __construct(int $count)
    {
        $this->weight = \array_fill(0, $count, 0);
    }

    /**
     * Получение результата работы нейрона
     *
     * @param array $input Входные данные
     * @return float
     */
    public function result(array $input): float
    {
        $this->input = $input;
        return $this->output = $this->activation($this->sum($input));
    }

    /**
     * Получение результата сумматора
     *
     * @param array $input Входные данные
     * @return float
     */
    public function sum(array $input): float
    {
        $sum = $this->bias;
        foreach ($input as $i => $val) {
            $sum += $val * $this->weight[$i];
        }
        return $sum;
    }

    /**
     * Функция активации
     *
     * @param float $value Сумма
     * @return float
     */
    abstract public function activation(float $value): float;

    /**
     * Производная функции активации
     *
     * @param float $value Сумма
     * @return float
     */
    abstract public function derivative(float $value): float;

    /**
     * Список свойств для сериализации
     *
     * @return array
     */
    public function __sleep(): array
    {
        return ['bias', 'weight'];
    }

}
