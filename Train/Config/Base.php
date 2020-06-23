<?php

declare(strict_types=1);

namespace NeuralNet\Train\Config;

/**
 * Настройки гиперпараметров обучения нейронной сети
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
class Base
{

    /**
     * @var float Скорость обучения [0.1]
     */
    public $rate = 0.1;

    /**
     * @var float Максимальная допустимая ошибка сети [0.01]
     */
    public $error = 0.01;

    /**
     * @var int Кол-во эпох обучения [1000]
     */
    public $epoch = 1000;

    /**
     * @var int Размер пакета обучения [50]
     */
    public $batch = 50;

}
