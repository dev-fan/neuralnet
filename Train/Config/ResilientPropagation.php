<?php

declare(strict_types=1);

namespace NeuralNet\Train\Config;

/**
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
class ResilientPropagation extends Base
{

    /**
     * @var float Максимальное значение дельты [50]
     */
    public $deltaMax = 50;

    /**
     * @var float Минимальное значение дельты [10e-6]
     */
    public $deltaMin = 10e-6;

    /**
     * @var float Коэффициент увеличения скорости [1.2]
     */
    public $rateMulti = 1.2;

    /**
     * @var float Коэффициент уменьшения скорости [0.5]
     */
    public $rateDiv= 0.5;

}
