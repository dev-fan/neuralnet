<?php

declare(strict_types=1);

namespace NeuralNet\Train;

/**
 * Базовый класс обучения нейронной сети
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2016 Gordon Freeman
 */
abstract class Base
{

    use \Math\Mean;
    use \NeuralNet\Helper\Verbose;

    /**
     * @var \NeuralNet\Train\Config\Base Настройки гиперпараметров обучения сети
     */
    protected $config;

    /**
     * @var \NeuralNet\Train\LossFunc\Base Функция потерь
     */
    protected $lossFunc;

    /**
     * @var \NeuralNet\Net\Base Нейронная сеть для обучения
     */
    protected $nn;

    /**
     * @var array Слои сети в обратном порядке
     */
    protected $nnReverse;

    /**
     * @var int Номер последнего раунда
     */
    protected $epoch;

    /**
     * @var float Средняя квадратическая ошибка сети после последнего раунда
     */
    protected $error;

    /**
     * @param \NeuralNet\Train\Config\Base $config Настройки гиперпараметров обучения сети
     * @param \NeuralNet\Train\LossFunc\Base $lossFunc Функция потерь
     * @param \NeuralNet\Net\Base $nn Тренируемая сеть
     */
    public function __construct(Config\Base $config, \NeuralNet\Train\LossFunc\Base $lossFunc, \NeuralNet\Net\Base $nn)
    {
        $this->config = $config;
        $this->lossFunc = $lossFunc;
        $this->nn = $nn;
        $this->nnReverse = \array_reverse($this->nn->getLayers(), true);
    }

    /**
     * Получение номера последнего раунда
     *
     * @return int
     */
    public function getEpoch(): int
    {
        return $this->epoch;
    }

    /**
     * Получение средней ошибки сети после последнего раунда
     *
     * @return float
     */
    public function getError(): float
    {
        return $this->error;
    }

    /**
     * Обучение нейронной сети.
     *
     * @param Data\Base $data Данные в формате array(array(Входные данные, Ожидаемый результат), ...)
     * @return bool true если обучение завершено, иначе false
     */
    public function train(Data\Base $data): bool
    {
        if ($this->verbose) {
            \printf('%s %6.1fMb | Data count: %d  %s'
                , \date('[Y-m-d H:i:s]')
                , \memory_get_usage() / 1048576
                , \count($data)
                , \PHP_EOL);
        }
        // Step 1. Пока условие прекращения работы алгоритма неверно, выполняются шаги 2 — 9.
        $this->epoch = 0;
        do {
            ++$this->epoch;
            $data->shuffle();
            $this->epoch($data);
            if ($this->verbose) { // Вывод инфы
                \printf('%s %6.1fMb | Round: %-5d | Error: %.12f   %s'
                        , \date('[Y-m-d H:i:s]')
                        , \memory_get_usage() / 1048576
                        , $this->epoch, $this->error
                        , $this->getEol($this->epoch));
            }
            // Step 9. Проверка условия прекращения работы алгоритма.
            // Условием прекращения работы алгоритма может быть как достижение суммарной
            // квадратичной ошибкой результата на выходе сети предустановленного заранее
            // минимума в ходе процесса обучения, так и выполнения определенного
            // количества итераций алгоритма. В основе алгоритма лежит метод под
            // названием градиентный спуск. В зависимости от знака, градиент функции
            // (в данном случае значение функции — это ошибка, а параметры — это
            // веса связей в сети) дает направление, в котором значения функции
            // возрастают (или убывают) наиболее стремительно.
        } while ($this->error > $this->config->error && $this->epoch < $this->config->epoch);
        if ($this->checkFinal($this->epoch)) {
            echo \PHP_EOL;
        }
        return $this->error <= $this->config->error;
    }

    /**
     * Выполнение одной эпохи обучения
     *
     * @param Data\Base $data Данные в формате array(array(Входные данные, Ожидаемый результат), ...)
     */
    abstract protected function epoch(Data\Base $data): void;

}
