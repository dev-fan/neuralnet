<?php

declare(strict_types=1);

namespace NeuralNet\Train\LossFunc;

/**
 * Чтоб найти производную dC/dw для вычисления градиента используем правило цепи и
 * раскаладываем dC/dw на dC/dA * dA/dS * dS/dw
 * dA/dS это производная функции активации нейрона
 * Метод dCdSo реализует вычисление dC/dA * dA/dS для выходного слоя
 * Метод dCdSh реализует вычисление dC/dA * dA/dS для скрытого слоя
 * dS/dw не реализуется в этом классе т.к. она равно входному сигналу нейрона и
 * это используется в \NeuralNet\Train\Backpropagation::backpropagate()
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2018 Gordon Freeman
 */
abstract class Base
{

    /**
     * @var array Корректировки смещения dC/dS (градиент = dCdS[$l][$n] * $neuron->input[$w])
     */
    protected $intermed = [];

    /**
     * Корректировка смещения
     *
     * @param int $l Номер слоя
     * @param int $n Номер нейрона
     * @return float
     */
    public function dCdS(int $l, int $n): float
    {
        return $this->intermed[$l][$n];
    }

    /**
     * Функция потерь
     *
     * @param array $actual Предсказанные значения
     * @param array $expect Ожидаемые значения
     */
    abstract public function loss(array $actual, array $expect): float;

    /**
     * Step 6. Вычисляет величину корректировки смещения выходного слоя: Dw[0k] = a * e[k]
     * и посылает e[k] нейронам в предыдущем слое.
     * Каждый выходной нейрон (Y[k], k=1,2,..,m) получает целевое значение —
     * то выходное значение, которое является правильным для
     * данного входного сигнала, и вычисляет ошибку: e[k] = (y[k] - t[k]) * f'(y_in[k])
     * так же потом вычисляет величину, на которую изменится вес связи w[jk]:
     * Dw[jk] = a * e[k] * z[j]
     *
     * @param int $l Номер слоя
     * @param int $n Номер нейрона
     * @param \NeuralNet\Neuron\Base $neuron Нейрон
     * @param float $actual Актуальный выход сети для текущего нейрона
     * @param float $expect Требуемый выход сети для текущего нейрона
     * @return float
     */
    abstract public function dCdSo(int $l, int $n, \NeuralNet\Neuron\Base $neuron, float $actual, float $expect): float;

    /**
     * Step 7. Вычисляет величину корректировки смещения скрытого: Dv[0k] = a * e[j]
     * Каждый скрытый нейрон (Z[j], j=1,2,..,p) суммирует входящие
     * ошибки (от нейронов в последующем слое) e_in[j] = SUM[k..n](e[k]) * w[jk])
     * и вычисляет величину ошибки, умножая полученное значение на
     * производную активационной функции: e[j] = e_in[j] * f'(z_in[j])
     * так же потом вычисляет величину, на которую изменится вес связи v[ij]:
     * Dv[ij] = a * e[j] * x[i]
     *
     * @param int $l Номер слоя
     * @param int $n Номер нейрона
     * @param \NeuralNet\Neuron $neuron Нейрон
     * @param \NeuralNet\Layer\Base $layer Скрытый слой
     * @return float
     */
    abstract public function dCdSh(int $l, int $n, \NeuralNet\Neuron\Base $neuron, \NeuralNet\Layer\Base $layer): float;

}
