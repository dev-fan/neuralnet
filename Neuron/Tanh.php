<?php

declare(strict_types=1);

namespace NeuralNet\Neuron;

/**
 * Нейрон с функцией гиперболического тангенса
 * f(x) = tanh(a * x)
 * f'(x) = a * (1 - tanh(a * x)^2
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2016 Gordon Freeman
 */
class Tanh extends Base
{

    /**
     * {@inheritDoc}
     */
    public function activation(float $value): float
    {
        return \tanh($value);
    }

    /**
     * {@inheritDoc}
     */
    public function derivative(float $value): float
    {
        return 1 - $value ** 2;
    }

}
