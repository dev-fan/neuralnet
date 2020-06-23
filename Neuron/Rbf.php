<?php

declare(strict_types=1);

namespace NeuralNet\Neuron;

/**
 * Радиально-базисная функция
 * f(x) = e^(-x^2 / a^2)
 * f'(x) = -2 * x * e^(-x^2)
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2016 Gordon Freeman
 */
class Rbf extends Base
{

    /**
     * {@inheritDoc}
     */
    public function activation(float $value): float
    {
        return \exp(-($value ** 2));
    }

    /**
     * {@inheritDoc}
     */
    public function derivative(float $value): float
    {
        return -2 * $value * \exp(-($value ** 2));
    }

}
