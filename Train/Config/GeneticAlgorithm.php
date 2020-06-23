<?php

declare(strict_types=1);

namespace NeuralNet\Train\Config;

/**
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
class GeneticAlgorithm extends Base
{

    /**
     * @var int Вероятность мутации 0-100 [20]
     */
    public $mutateLikelihood = 20;

    /**
     * @var int Максимальное количество единиц населения [10]
     */
    public $maxUnits = 10;

    /**
     * @var int Кол-во высших единиц (победителей), используемых для развития населения [4]
     */
    public $topUnits = 4;

}
