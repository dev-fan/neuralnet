<?php

declare(strict_types=1);

namespace NeuralNet\Train\Config;

/**
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
class Backpropagation extends Base
{

    /**
     * @var float Момент обучения [0.8]
     */
    public $momentum = 0.8;

    /**
     * @var float Коэффициент L1 регуляризации [0.01]
     */
    public $lambdaL1 = 0.01;

    /**
     * @var float Коэффициент L1 регуляризации [0.01]
     */
    public $lambdaL2 = 0.01;

}
