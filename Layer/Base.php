<?php

declare(strict_types=1);

namespace NeuralNet\Layer;

/**
 * Базовый слой нейросети
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2018 Gordon Freeman
 */
class Base extends \SplFixedArray
{

    /**
     * Вычислить выходной сигнал нейронной сети для указанных данных
     *
     * @param array $input Входные данные
     * @return array Выходные данные
     */
    public function result(array $input): array
    {
        $output = [];
        foreach ($this as $n => $neuron) {
            /* @var $neuron \NeuralNet\Neuron\Base */
            // Step 4. Каждый скрытый нейрон (Z[j], j=1,2,..,p) суммирует взвешенные
            // входящие сигналы: z_in[j] = v[0j] + SUM[i..n](x[i] * v[ij]) и
            // применяет активационную функцию: z[j] = f(z_in[j]).
            $output[$n] = $neuron->result($input);
        }
        return $output;
    }

    /**
     * Создание списка слоёв по параметрам
     *
     * @param array $params Массив с кол-вом нейронов в слоях
     * @param array $classes Список классов нейронов по слоям без учета входного слоя
     * @return array Слои с нейронами
     * @throws \InvalidArgumentException
     */
    public static function createLayers(array $params, array $classes): array
    {
        if (\count($params) < 2) {
            throw new \InvalidArgumentException('Must be at least 2 layers', 20);
        }
        if (\count($params) !== \count($classes) + 1) {
            throw new \InvalidArgumentException('Size of the array with a list of classes to be equal to the count of layers - 1', 30);
        }
        // Step 0. Инициализация весов (или веса всех связей инициализируются
        // случайными небольшими значениями через NeuralNet\Init\Random).
        $countInput = \array_shift($params);
        $layers = [];
        foreach ($params as $num => $count) {
            $class = $classes[$num];
            $layers[$num] = static::createLayer($countInput, $count, $class);
            $countInput = $count;
        }
        return $layers;
    }

    /**
     * Создание слоя по параметрам
     *
     * @param int $input Кол-во входов нейронов
     * @param int $count Кол-во нейронов
     * @param string $class Класс нейрона
     * @return \NeuralNet\Layer\Base
     * @throws \InvalidArgumentException
     */
    public static function createLayer(int $input, int $count, string $class): self
    {
        if (!\class_exists($class)) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" not found', $class), 40);
        }
        if (!\in_array(\NeuralNet\Neuron\Base::class, \class_parents($class))) {
            throw new \InvalidArgumentException(\sprintf('Class "%s" must be an instance of %s', $class, \NeuralNet\Neuron\Base::class), 10);
        }
        $layer = new static($count);
        for ($i = 0; $i < $count; $i++) {
            $layer[$i] = new $class($input);
        }
        return $layer;
    }

}
