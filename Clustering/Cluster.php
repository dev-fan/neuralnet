<?php

declare(strict_types=1);

namespace NeuralNet\Clustering;

/**
 * Cluster
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2019 Gordon Freeman
 */
class Cluster extends \ArrayIterator
{

    /**
     * Расчёт центроида множества точек
     *
     * @return \NeuralNet\Clustering\Point
     */
    public function centroid(): Point
    {
        $cntCoords = \count($this->current()->getCoordinates());
        $cntPoints = \count($this);
        $sums = \array_fill(0, $cntCoords, 0);
        foreach ($this as $point) {
            /** @var Point $point */
            for ($k = 0; $k < $cntCoords; $k++) {
                $sums[$k] += $point[$k];
            }
        }
        $coords = \array_map(function ($v) use ($cntPoints) { return $v / $cntPoints; }, $sums);
        return new Point($coords, 'Centroid');
    }

}
