<?php

declare(strict_types=1);

namespace NeuralNet\Net;

/**
 * Базовый класс нейронной сети
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2016 Gordon Freeman
 */
abstract class Base
{

    /**
     * @var array Слои нейронов
     */
    protected $layers = [];

    /**
     * Вычислить выходной сигнал нейронной сети для указанных данных
     *
     * @param array $input Входные данные
     * @return array Выходные данные
     */
    abstract public function result(array $input): array;

    /**
     * Получить все слои нейронов
     *
     * @return array
     */
    public function getLayers(): array
    {
        return $this->layers;
    }

    /**
     * Получить указанный слой нейронов
     *
     * @param int $i Номер слоя
     * @return \NeuralNet\Layer\Base
     */
    public function getLayer(int $i): \NeuralNet\Layer\Base
    {
        return $this->layers[$i];
    }

    /**
     * Читабельный вывод нейронной сети
     *
     * @return void
     */
    public function dump(): void
    {
        foreach ($this->layers as $l => $layer) {
            foreach ($layer as $n => $neuron) {
                \printf("output = %.8f, bias = %.8f\n", $neuron->output, $neuron->bias);
                foreach ($neuron->weight as $w => $weight) {
                    \printf("%s [%d][%d][%02d] = %.8f\n", \get_class($neuron), $l, $n, $w, $weight);
                }
            }
        }
    }

    /**
     * Список свойств для сериализации
     *
     * @return array
     */
    public function __sleep(): array
    {
        return ['layers'];
    }

    /**
     * Клонирование сети
     *
     * @return void
     */
    public function __clone()
    {
        foreach ($this->layers as $l => $layer) {
            $this->layers[$l] = clone $layer;
            foreach ($this->layers[$l] as $n => $neuron) {
                $this->layers[$l][$n] = clone $neuron;
            }
        }
    }

}
