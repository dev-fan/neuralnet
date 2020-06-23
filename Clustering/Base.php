<?php

declare(strict_types=1);

namespace NeuralNet\Clustering;

/**
 * Base cluster
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2019 Gordon Freeman
 */
abstract class Base
{

    use \NeuralNet\Helper\Verbose;

    /**
     * @var \NeuralNet\Clustering\Config\Base Настройки гиперпараметров кластеризации
     */
    protected $config;

    /**
     * @param \NeuralNet\Clustering\Config\Base $config Настройки гиперпараметров кластеризации
     */
    public function __construct(Config\Base $config)
    {
        $this->config = $config;
    }

    /**
     * Кластеризация
     *
     * @param Point[] $points Список точек
     * @return Cluster[] Список кластеров
     */
    abstract public function cluster(array $points): array;

}
