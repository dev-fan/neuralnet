<?php

declare(strict_types=1);

namespace NeuralNet\Neuron;

/**
 * Нейрон с сигмоидальной функцией (softmax)
 * f(x) = 1 / 1 + e^(-a * x)
 * f'(x) = a * x * (1 - x)
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2016 Gordon Freeman
 */
class Sigmoid extends Base
{

    /**
     * {@inheritDoc}
     */
    public function activation(float $value): float
    {
        return 1.0 / (1.0 + \exp(- $value));
    }

    /**
     * {@inheritDoc}
     */
    public function derivative(float $value): float
    {
        return $value * (1.0 - $value);
    }

}
