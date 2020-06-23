<?php

declare(strict_types=1);

namespace NeuralNet\Neuron;

/**
 * Нейрон с пороговой функцией
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2016 Gordon Freeman
 */
class Threshold extends Base
{

    /**
     * @var int Кол-во входов
     */
    protected $count;

    /**
     * {@inheritDoc}
     */
    public function __construct(int $count)
    {
        parent::__construct($count);
        $this->count = $count;
    }

    /**
     * {@inheritDoc}
     */
    public function activation(float $value): float
    {
        return $value >= $this->count ? 1 : 0;
    }

    /**
     * {@inheritDoc}
     */
    public function derivative(float $value): float
    {
        return 1;
    }

}
