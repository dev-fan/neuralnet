<?php

declare(strict_types=1);

namespace NeuralNet\Init;

/**
 * Базовый класс инициализации весов нейронной сети
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
abstract class Base
{

    /**
     * Инициализация весов нейронной сети.
     *
     * @param \NeuralNet\Net\Base $nn Нейронная сеть
     * @return void
     */
    abstract public function init(\NeuralNet\Net\Base $nn): void;

}
