<?php

declare(strict_types=1);

namespace NeuralNet\Init;

/**
 * Рандомная инициализация весов нейронной сети
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2016 Gordon Freeman
 */
class Random extends Base
{

    private const SCALE = 1000;

    /**
     * @var array [min, max]
     */
    protected $range = [-0.1, 0.1];

    /**
     * @param float $min Минимальной значение диапазона инициализации [-0.1]
     * @param float $max Максимальное значение диапазона инициализации [0.1]
     */
    public function __construct(float $min = -0.1, float $max = 0.1)
    {
        $this->range = [$min, $max];
    }

    /**
     * {@inheritDoc}
     */
    public function init(\NeuralNet\Net\Base $nn): void
    {
        // Step 0. Инициализация весов (или веса всех связей инициализируются
        // случайными небольшими значениями через NeuralNet\Init\Random).
        $min = \intval($this->range[0] * self::SCALE);
        $max = \intval($this->range[1] * self::SCALE);
        foreach ($nn->getLayers() as $layer) {
            foreach ($layer as $neuron) {
                /* @var $neuron \NeuralNet\Neuron\Base */
                foreach ($neuron->weight as &$weight) {
                    $weight = \mt_rand($min, $max) / self::SCALE;
                }
                $neuron->bias = \mt_rand($min, $max) / self::SCALE;
            }
        }
    }

}
