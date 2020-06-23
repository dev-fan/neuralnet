<?php

declare(strict_types=1);

namespace NeuralNet\Neuron;

/**
 * Применяется со слоем Softmax
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2018 Gordon Freeman
 */
class Softmax extends Base
{

    /**
     * {@inheritDoc}
     */
    public function activation(float $value): float
    {
        return \exp($value);
    }

    /**
     * {@inheritDoc}
     */
    public function derivative(float $value): float
    {
        return $value * (1 - $value);
    }

}
