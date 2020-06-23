<?php

declare(strict_types=1);

namespace NeuralNet\Train\Config;

/**
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
class ContrastiveDivergence extends Base
{

    /**
     * @var int Кол-во сэмплирований [3]
     */
    public $sampling = 3;

}
