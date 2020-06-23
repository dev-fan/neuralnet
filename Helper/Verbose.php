<?php

declare(strict_types=1);

namespace NeuralNet\Helper;

/**
 * Вывод инфы
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2019 Gordon Freeman
 */
trait Verbose
{

    /**
     * @var int Уровень вывода
     */
    protected $verbose;

    /**
     * @var array Вывод инфы в указанные итерации
     */
    protected $verboseIterations = [
        1       => 0,
        2       => 0,
        4       => 0,
        8       => 0,
        16      => 0,
        32      => 0,
        64      => 0,
        128     => 0,
        256     => 0,
        512     => 0,
        1024    => 0,
        2048    => 0,
        4096    => 0,
        8192    => 0,
        16384   => 0,
        32768   => 0,
        65536   => 0,
        131072  => 0,
        262144  => 0,
        524288  => 0,
        1048576 => 0,
    ];

    /**
     * Установка подробного вывода:
     * 1 - \n как перенос
     * 2 - \r как перенос, но \n по указанным итерациям
     * 4 - \r как перенос всегда
     *
     * @param int $verbose Уровень вывода
     * @return self
     */
    public function setVerbose(int $verbose): self
    {
        $this->verbose = $verbose;
        return $this;
    }

    /**
     * Установка списка итераций в которые будет вывод, по-умолчанию степени двойки
     *
     * @param array $verboseIterations Массив с ключами номеров итерации
     * @return self
     */
    public function setVerboseEpochs(array $verboseIterations): self
    {
        $this->verboseIterations = $verboseIterations;
        return $this;
    }

    /**
     * Конец строки для указанной итерации
     *
     * @param int $iteration Номер текущей итерации
     * @return string
     */
    public function getEol(int $iteration): string
    {
        return ($this->verbose & 1 || ($this->verbose & 2 && isset($this->verboseIterations[$iteration]))) ? \PHP_EOL : "\r";
    }

    /**
     * Проверка надобности переноса в конце итераций для $this->verbose = 6
     *
     * @param int $iteration Номер последней итерации
     * @return bool
     */
    public function checkFinal(int $iteration): bool
    {
        return $this->verbose & 6 && !isset($this->verboseIterations[$iteration]);
    }

}
