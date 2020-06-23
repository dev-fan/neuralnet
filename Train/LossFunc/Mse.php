<?php

declare(strict_types=1);

namespace NeuralNet\Train\LossFunc;

/**
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2018 Gordon Freeman
 */
class Mse extends Base
{

    /**
     * {@inheritDoc}
     */
    public function loss(array $actual, array $expect): float
    {
        $sum = 0.0;
        foreach ($actual as $i => $val) {
            $sum += ($val - $expect[$i]) ** 2;
        }
        return $sum / 2;
    }

    /**
     * {@inheritDoc}
     */
    public function dCdSo(int $l, int $n, \NeuralNet\Neuron\Base $neuron, float $actual, float $expect): float
    {
        return $this->intermed[$l][$n] = ($actual - $expect) * $neuron->derivative($neuron->output);
    }

    /**
     * {@inheritDoc}
     */
    public function dCdSh(int $l, int $n, \NeuralNet\Neuron\Base $neuron, \NeuralNet\Layer\Base $layer): float
    {
        $productsum = 0;
        $intermedUp = $this->intermed[$l + 1];
        foreach ($layer as $k => $neuronUp) {
            /* @var $neuronUp \NeuralNet\Neuron\Base */
            $productsum += $intermedUp[$k] * $neuronUp->weight[$n];
        }
        return $this->intermed[$l][$n] = $productsum * $neuron->derivative($neuron->output);
    }

}
