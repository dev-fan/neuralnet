<?php

declare(strict_types=1);

namespace NeuralNet\Clustering;

/**
 * Point
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2019 Gordon Freeman
 */
class Point implements \ArrayAccess
{

    /**
     * @var array Координаты точки
     */
    protected $coordinates = [];

    /**
     * @var string Метка точки
     */
    protected $label;

    /**
     * @param array $coordinates Координаты
     * @param string $label Метка
     */
    public function __construct(array $coordinates, string $label = null)
    {
        $this->coordinates = $coordinates;
        $this->label = $label;
    }

    /**
     * Координаты
     *
     * @return array
     */
    public function getCoordinates(): array
    {
        return $this->coordinates;
    }

    /**
     * Метка
     *
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * Расстояние между точками
     *
     * @param self $point
     * @return float
     */
    public function distance(self $point): float
    {
        $sum = 0.0;
        $coordinates = $point->getCoordinates();
        for ($i = \count($coordinates) - 1; $i >= 0; --$i) {
            $sum += ($this->coordinates[$i] - $coordinates[$i]) ** 2;
        }
        return \sqrt($sum);
    }

    /**
     * @param int $i
     */
    public function offsetExists($i): bool
    {
        return isset($this->coordinates[$i]);
    }

    /**
     * @param int $i
     * @return float
     */
    public function offsetGet($i)
    {
        return $this->coordinates[$i];
    }

    /**
     * @param int $i
     * @param float $val
     */
    public function offsetSet($i, $val): void
    {
        $this->coordinates[$i] = $val;
    }

    /**
     * @param int $i
     */
    public function offsetUnset($i): void
    {
        unset($this->coordinates[$i]);
    }

}
