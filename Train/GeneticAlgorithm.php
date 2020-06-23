<?php

declare(strict_types=1);

namespace NeuralNet\Train;

/**
 * Генетический алгоритм.
 * https://habrahabr.ru/post/128704/
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
class GeneticAlgorithm extends Base
{

    /**
     * @var array Популяция
     */
    public $population = [];

    /**
     * @param \NeuralNet\Train\Config\GeneticAlgorithm $config Настройки обучения сети
     * @param \NeuralNet\Net\Base[] $population
     */
    public function __construct(Config\GeneticAlgorithm $config, array $population)
    {
        foreach ($population as $net) {
            if (!$net instanceof \NeuralNet\Net\Base) {
                throw new \InvalidArgumentException(\sprintf('One population must be an instance of %s', \NeuralNet\Net\Base::class), 10);
            }
        }
        $this->config = $config;
        $this->population = $population;
        if ($this->config->maxUnits < $this->config->topUnits) {
            $this->config->topUnits = $this->config->maxUnits;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function loss(array $actual, array $expect): float
    {
        $sum = 0.0;
        foreach ($actual as $i => $val) {
            $sum += ($val - $expect[$i]) ** 2;
        }
        return $sum / 2;
    }

    /**
     * {@inheritDoc}
     */
    protected function epoch(Data\Base $data): void
    {
        $this->error = \PHP_INT_MAX;
        foreach ($this->population as $nn) {
            /* @var $nn \NeuralNet\Net\Base */
            $errors = [];
            foreach ($data as list($input, $expected)) {
                // Step 3 и Step 5. Описание в $nn->result(), Step 4 Описание в $layer->result()
                $output = $nn->result($input);
                $errors[] = $this->loss($output, $expected);
            }
            $error = $this->mean($errors);
            $nn->fitness = 1 - $error;
            $this->error = \min($error, $this->error);
        }
        $this->evolve();
    }

    /**
     * Эволюция, выполняет селекцию, скрещивание и мутации у населения
     */
    protected function evolve(): void
    {
        $winners = $this->selection();
        // Заполняем оставшуюся часть следующей группы новыми единицами,
        // используя скрещивание и мутацию
        for ($i = $this->config->topUnits; $i < $this->config->maxUnits; $i++) {
            if ($i == $this->config->topUnits) {
                // Скрещивание двух лучших победителей
                $parentA = clone $winners[0];
                $parentB = clone $winners[1];
                $offspring = $this->crossover($parentA, $parentB);
            } elseif ($i < $this->config->maxUnits - 2) {
                // Скрещивание двух случайных победителей
                $keys = \array_rand($winners, 2);
                $parentA = clone $winners[$keys[0]];
                $parentB = clone $winners[$keys[1]];
                $offspring = $this->crossover($parentA, $parentB);
            } else {
                // Копирование случайного победителя
                $offspring = clone $winners[\array_rand($winners)];
            }
            // Мутация
            $offspring = $this->mutation($offspring);
            $offspring->fitness = 0;
            // обновить популяцию, изменив старого жителя на нового
            $this->population[$i] = $offspring;
        }
    }

    /**
     * Селекция, отбор лучших из текущей популяции
     * 
     * @return array
     */
    protected function selection(): array
    {
        \usort($this->population, function ($a, $b) {
            return $b->fitness <=> $a->fitness;
        });
        return \array_slice($this->population, 0, $this->config->topUnits);
    }

    /**
     * Скрещивание двух родителей
     * 
     * @param \NeuralNet\Net\Base $parentA
     * @param \NeuralNet\Net\Base $parentB
     * @return \NeuralNet\Net\Base
     */
    protected function crossover(\NeuralNet\Net\Base $parentA, \NeuralNet\Net\Base $parentB): \NeuralNet\Net\Base
    {
        foreach ($parentA->getLayers() as $l => $layerA) {
            $layerB = $parentB->getLayer($l);
            // получить точку пересечения
            $countN = \count($layerA);
            $point = \mt_rand(0, $countN - 1);
            // обменивать «смещение» информации между обоими родителями:
            // 1. левая сторона точки пересечения копируется из одного родителя.
            // 2. правая сторона после того, как точка пересечения копируется из второго родителя.
            for ($i = $point; $i < $countN; $i++) {
                $biasA = $layerA[$i]->bias;
                $layerA[$i]->bias = $layerB[$i]->bias;
                $layerB[$i]->bias = $biasA;
            }
        }
        return \mt_rand(0, 9) % 2 ? $parentA : $parentB;
    }

    /**
     * Случайные мутации у потомства
     * 
     * @param \NeuralNet\Net\Base $offspring
     * @return \NeuralNet\Net\Base
     */
    protected function mutation(\NeuralNet\Net\Base $offspring): \NeuralNet\Net\Base
    {
        foreach ($offspring->getLayers() as $layer) {
            foreach ($layer as $neuron) {
                /* @var $neuron \NeuralNet\Neuron\Base */
                foreach ($neuron->weight as &$weight) {
                    $weight = $this->mutate($weight);
                }
                $neuron->bias = $this->mutate($neuron->bias);
            }
        }
        return $offspring;
    }

    /**
     * Мутация гена [* -1:3]
     *
     * @param float $val
     * @return float
     */
    protected function mutate(float $val): float
    {
        if (\mt_rand(0, 100) < $this->config->mutateLikelihood) {
            $factor = 100 + ((\mt_rand(0, 100) - 50) * 3 + (\mt_rand(0, 100) - 50));
            $val *= $factor / 100;
        }
        return $val;
    }

}
