<?php

declare(strict_types=1);

namespace NeuralNet\Clustering;

/**
 * Density-based spatial clustering of applications with noise
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2019 Gordon Freeman
 */
class Dbscan extends Base
{

    /**
     * @var Point[] Список точек
     */
    protected $points;

    /**
     * @var Point[] Список точек, не назначенных кластеру (шум)
     */
    protected $noises = [];

    /**
     * {@inheritDoc}
     */
    public function cluster(array $points): array
    {
        $this->noises = [];
        $this->points = $points;
        $epsilon = $this->config->epsilon;
        $minpoints = $this->config->minpoints;
        $inCluster = new \SplObjectStorage();
        // Кластеризация
        $c = 0;
        $clusters = [];
        foreach ($this->points as $p => $point) {
            if ($this->verbose) { // Вывод инфы
                \printf('%s %6.1fMb | Handle point: %-5d | Clusters count: %-5d   %s'
                        , \date('[Y-m-d H:i:s]')
                        , \memory_get_usage() / 1048576
                        , $p
                        , \count($clusters)
                        , $this->getEol($p));
            }
            $neighbors = $this->getNeighbors($point, $epsilon);
            if (\count($neighbors) < $minpoints) {
                $this->noises[] = $point;
            } elseif (!isset($inCluster[$point])) {
                // Expand cluster
                $clusters[$c] = new Cluster();
                $clusters[$c][] = $point;
                $inCluster[$point] = 1;
                $neighborA = \reset($neighbors);
                while ($neighborA) {
                    $neighborsB = $this->getNeighbors($neighborA, $epsilon);
                    if (\count($neighborsB) >= $minpoints) {
                        foreach ($neighborsB as $neighborB) {
                            $neighbors[\spl_object_id($neighborB)] = $neighborB;
                        }
                    }
                    if (!isset($inCluster[$neighborA])) {
                        $clusters[$c][] = $neighborA;
                        $inCluster[$neighborA] = 1;
                    }
                    $neighborA = \next($neighbors);
                }
                $c++;
            }
        }
        if ($this->checkFinal($p)) {
            echo \PHP_EOL;
        }
        return $clusters;
    }

    /**
     * Поиск соседних точек в пределах $epsilon
     *
     * @param Point $pointA
     * @param float $epsilon
     * @return Point[]
     */
    protected function getNeighbors(Point $pointA, float $epsilon): array
    {
        $neighbors = [];
        foreach ($this->points as $pointB) {
            if ($pointA != $pointB) {
                if ($pointA->distance($pointB) < $epsilon) {
                    $neighbors[\spl_object_id($pointB)] = $pointB;
                }
            }
        }
        return $neighbors;
    }

}
