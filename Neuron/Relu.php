<?php

declare(strict_types=1);

namespace NeuralNet\Neuron;

/**
 * Нейроны с данной функцией активации называются ReLU (Rectified linear unit).
 * ReLU имеет следующую формулу f(x) = max(0, x) и реализует простой
 * пороговый переход в нуле.
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2016 Gordon Freeman
 */
class Relu extends Base
{

    /**
     * {@inheritDoc}
     */
    public function activation(float $value): float
    {
        return \max($value, 0);
    }

    /**
     * {@inheritDoc}
     */
    public function derivative(float $value): float
    {
        return $value > 0 ? 1 : 0;
    }

}
