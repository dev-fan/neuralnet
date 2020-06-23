<?php

declare(strict_types=1);

namespace NeuralNet\Clustering\Config;

/**
 * Настройки гиперпараметров кластеризации
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2019 Gordon Freeman
 */
class Base
{

    /**
     * @var int Кол-во попыток кластеризации [1000]
     */
    public $epoch = 1000;

    /**
     * @var int Требуемое кол-во кластеров [0]
     */
    public $countClusters = 0;

    /**
     * @var float Минимальное расстояние для скопления вокруг
     */
    public $epsilon = 0;

    /**
     * @var int Минимальное количество точек в эпсилоне другой точки, необходимое для формирования кластера
     */
    public $minpoints = 0;

}
