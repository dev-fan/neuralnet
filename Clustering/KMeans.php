<?php

declare(strict_types=1);

namespace NeuralNet\Clustering;

/**
 * KMeans
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2019 Gordon Freeman
 */
class KMeans extends Base
{

    /**
     * {@inheritDoc}
     */
    public function cluster(array $points): array
    {
        if ($this->config->countClusters <= 0) {
            throw new \InvalidArgumentException('Invalid clusters number');
        }
        $result = [];
        // Выбираем случайные центры кластеров
        $minX = $minY = \PHP_INT_MAX;
        $maxX = $maxY = \PHP_INT_MIN;
        foreach ($points as $point) {
            [$x, $y] = $point->getCoordinates();
            $minX = $minX > $x ? $x : $minX;
            $minY = $minY > $y ? $y : $minY;
            $maxX = $maxX < $x ? $x : $maxX;
            $maxY = $maxY < $y ? $y : $maxY;
        }
        $centroids = [];
        for ($k = 0; $k < $this->config->countClusters; $k++) {
            $centroids[$k] = new Point([\mt_rand($minX, $maxX), \mt_rand($minY, $maxY)]);
        }
        $epoch = 0;
        while (++$epoch <= $this->config->epoch) {
            if ($this->verbose) { // Вывод инфы
                \printf('%s %6.1fMb | Round: %-5d   %s'
                        , \date('[Y-m-d H:i:s]')
                        , \memory_get_usage() / 1048576
                        , $epoch
                        , $this->getEol($epoch));
            }
            $clusters = [];
            foreach ($points as $p => $point) {
                /** @var Point $point */
                // Расчитываем расстояния до центров кластеров для каждой точки
                $distances = [];
                foreach ($centroids as $c => $centroid) {
                    $distances[$c] = $centroid->distance($point);
                }
                // Назначаем точки кластерам
                $idxC = \array_search(\min($distances), $distances);
                $clusters[$idxC] = $clusters[$idxC] ?? new Cluster();
                $clusters[$idxC][$p] = $point;
            }
            if (\count($clusters) < $this->config->countClusters) {
                // Если кластеров недостаточно, берем случайный элемент случайного кластера и делаем его кластером.
                $idxC = \array_rand($clusters);
                $idxP = \array_rand($clusters[$idxC]->getArrayCopy());
                for ($k = 0; $k < $this->config->countClusters; $k++) {
                    if (empty($clusters[$k])) {
                        $clusters[$k] = new Cluster();
                        $clusters[$k][$idxP] = $clusters[$idxC][$idxP];
                        break;
                    }
                }
                unset($clusters[$idxC][$idxP]);
            }
            // Определяем новый центр кластеров
            $newCentroids = [];
            foreach ($clusters as $cluster) {
                $newCentroids[] = $cluster->centroid();
            }
            // Если координаты центра не изменились, возвращаем результат
            if ($centroids == $newCentroids) {
                $result = $clusters;
                break;
            }
            $centroids = $newCentroids;
        }
        if ($this->checkFinal($epoch)) {
            echo \PHP_EOL;
        }
        return $result;
    }

}
