<?php

declare(strict_types=1);

namespace NeuralNet\Train\LossFunc;

/**
 * Обучение методом обратного распространения ошибки с целевой функцией кросс-энтропии.
 * C = - SUM[y * ln(a) + (1 -y) * ln(1 -a)] / n
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2018 Gordon Freeman
 */
class CrossEntropy extends Mse
{

    /**
     * {@inheritDoc}
     */
    public function loss(array $actual, array $expect): float
    {
        $sum = 0.0;
        foreach ($actual as $i => $val) {
            if ($expect[$i] == 1) {
                if ($val < 1e-320) {
                    $sum -= -1000;
                } else {
                    $sum -= $expect[$i] * \log($val);
                }
            } elseif ($val == 1) {
                $sum -= -1000;
            } else {
                $sum -= \log(1 - $val);
            }
        }
        return $sum / \count($actual);
    }

    /**
     * {@inheritDoc}
     */
    public function dCdSo(int $l, int $n, \NeuralNet\Neuron\Base $neuron, float $actual, float $expect): float
    {
        return $this->intermed[$l][$n] = $actual - $expect;
    }

}
