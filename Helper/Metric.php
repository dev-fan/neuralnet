<?php

declare(strict_types=1);

namespace NeuralNet\Helper;

/**
 * Метрики результатов обучения в задачах классификации
 * https://habrahabr.ru/company/ods/blog/328372/
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
class Metric
{

    /**
     * @var int True positive
     */
    public $tp = 0;

    /**
     * @var int True negative
     */
    public $tn = 0;

    /**
     * @var int False positive (ошибка I-го рода)
     */
    public $fp = 0;

    /**
     * @var int False negative (ошибка II-го рода)
     */
    public $fn = 0;

    /**
     * Суммарное количество
     *
     * @return int
     */
    public function count(): int
    {
        return $this->tp + $this->tn + $this->fp + $this->fn;
    }

    /**
     * True Positive Rate (TPR) - способность алгоритма обнаруживать данный
     * класс вообще, другое название полнота (recall).
     * Не зависит, в отличие от accuracy, от соотношения классов и потому
     * применимы в условиях несбалансированных выборок.
     *
     * @return float
     */
    public function tpr(): float
    {
        $sum = $this->tp + $this->fn;
        if ($sum > 0) {
            return $this->tp / $sum;
        }
        return 0;
    }

    /**
     * False Positive Rate (FPR) - доля из объектов negative класса которые
     * алгоритм предсказал неверно
     *
     * @return float
     */
    public function fpr(): float
    {
        $sum = $this->fp + $this->fn;
        if ($sum > 0) {
            return $this->fp / $sum;
        }
        return 0;
    }

    /**
     * Аккуратность - доля правильных ответов алгоритма.
     * Не применимы в условиях несбалансированных выборок.
     *
     * @return float
     */
    public function accuracy(): float
    {
        if ($this->count()) {
            return ($this->tp + $this->tn) / $this->count();
        }
        return 0;
    }

    /**
     * Точность - способность отличать этот класс от других классов.
     * Не зависит, в отличие от accuracy, от соотношения классов и потому
     * применимы в условиях несбалансированных выборок.
     *
     * @return float
     */
    public function precision(): float
    {
        $sum = $this->tp + $this->fp;
        if ($sum > 0) {
            return $this->tp / $sum;
        }
        return 0;
    }

    /**
     * Полнота
     *
     * @return float
     */
    public function recall(): float
    {
        return $this->tpr();
    }

    /**
     * F-мера (в общем случае  Fβ) — среднее гармоническое precision и recall.
     *
     * @param float $b Beta [1]
     * @return float
     */
    public function fscore($b = 1): float
    {
        $precision = $this->precision();
        $recall = $this->tpr();
        if (!$precision || !$recall) {
            return 0;
        }
        return (1 + $b ** 2) * $precision * $recall
            / (($b ** 2 * $precision) + $recall);
    }

}
