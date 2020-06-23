<?php

declare(strict_types=1);

namespace NeuralNet\Train;

/**
 * Обучение методом упругого распространения.
 * https://en.wikipedia.org/wiki/Rprop
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
class ResilientPropagation extends Base
{

    /**
     * @var int Индекс выходного слоя сети
     */
    protected $idxOutlayer;

//    /**
//     * @var array Коррекция весов предыдущего шага
//     */
//    protected $prevWeightDelta = [];

    /**
     * @var array Дельта предыдущего шага
     */
    protected $prevRate = [];

    /**
     * @var array Предыдущие значения градиента
     */
    protected $prevGradient = [];

    /**
     * {@inheritDoc}
     */
    public function __construct(Config\ResilientPropagation $config, \NeuralNet\Train\LossFunc\Base $lossFunc, \NeuralNet\Net\FeedForward $nn)
    {
        parent::__construct($config, $lossFunc, $nn);
        $this->idxOutlayer = \count($this->nn->getLayers()) - 1;
        foreach ($this->nn->getLayers() as $l => $layer) {
            foreach ($layer as $n => $neuron) {
                $count = \count($neuron->weight) + 1;
                $this->prevGradient[$l][$n] = \array_fill(0, $count, 0);
                $this->prevRate[$l][$n] = \array_fill(0, $count, $this->config->rate);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function epoch(Data\Base $data): void
    {
        $intermed = []; // Промежуточный градиент (градиент = $intermed[$l][$n] * $neuron->input[$w])
        $errors = [];
        $deltaMax = $this->config->deltaMax;
        $deltaMin = $this->config->deltaMin;
        $rateMulti = $this->config->rateMulti;
        $rateDiv= $this->config->rateDiv;
        foreach ($data as list($input, $expected)) {
            // Step 3 и Step 5. Описание в $nn->result(), Step 4 Описание в $layer->result()
            $output = $this->nn->result($input);
            $errors[] = $this->lossFunc->loss($output, $expected);
            foreach ($this->nnReverse as $l => $layer) {
                foreach ($layer as $n => $neuron) {
                    /* @var $neuron \NeuralNet\Neuron\Base */
                    if ($l == $this->idxOutlayer) {
                        $err = $output[$n] - $expected[$n];
                        $intermed[$l][$n] = $err * $neuron->derivative($neuron->output);
                    } else {
                        $intermedUp = $intermed[$l + 1];
                        $productsum = 0;
                        foreach ($this->nn->getLayer($l + 1) as $k => $neuronUp) {
                            /* @var $neuronUp \NeuralNet\Neuron\Base */
                            $productsum += $intermedUp[$k] * $neuronUp->weight[$n];
                        }
                        $intermed[$l][$n] = $productsum * $neuron->derivative($neuron->output);
                    }
                }
            }
            foreach ($this->nn->getLayers() as $l => $layer) {
                foreach ($layer as $n => $neuron) {
                    /* @var $neuron \NeuralNet\Neuron\Base */
                    $gradHalf = $intermed[$l][$n];
                    $gradTmp = [];
                    $prevRateTmp = &$this->prevRate[$l][$n];
                    $prevGradTmp = $this->prevGradient[$l][$n];
                    foreach ($neuron->weight as $w => $weight) {
                        $sign = $neuron->input[$w] * $gradHalf <=> 0;
                        $isChange = $sign * $prevGradTmp[$w];
                        if ($isChange > 0) {
                            $rate = \min($deltaMax, $prevRateTmp[$w] * $rateMulti);
                            $delta = $sign * $rate;
                            $neuron->weight[$w] -= $delta;
//                            $this->prevWeightDelta[$l][$n][$w] = $delta;
                            $gradTmp[$w] = $sign;
                        } elseif ($isChange < 0) {
                            $rate = \max($deltaMin, $prevRateTmp[$w] * $rateDiv);
//                            $neuron->weight[$w] += $this->prevWeightDelta[$l][$n][$w];
                            $gradTmp[$w] = 0;
                        } else {
                            $rate = $prevRateTmp[$w];
                            $delta = $sign * $rate;
                            $neuron->weight[$w] -= $delta;
//                            $this->prevWeightDelta[$l][$n][$w] = $delta;
                            $gradTmp[$w] = $sign;
                        }
                        $prevRateTmp[$w] = $rate;
                    } // weights
                    // bias
                    $o = $w + 1;
                    $sign = $gradHalf <=> 0;
                    $isChange = $sign * $prevGradTmp[$o];
                    if ($isChange > 0) {
                        $rate = \min($deltaMax, $prevRateTmp[$o] * $rateMulti);
                        $delta = $sign * $rate;
                        $neuron->bias -= $delta;
//                        $this->prevWeightDelta[$l][$n][$o] = $delta;
                        $gradTmp[$o] = $sign;
                    } elseif ($isChange < 0) {
                        $rate = \max($deltaMin, $prevRateTmp[$o] * $rateDiv);
//                        $neuron->bias += $this->prevWeightDelta[$l][$n][$o];
                        $gradTmp[$o] = 0;
                    } else {
                        $rate = $prevRateTmp[$o];
                        $delta = $sign * $rate;
                        $neuron->bias -= $delta;
//                        $this->prevWeightDelta[$l][$n][$o] = $delta;
                        $gradTmp[$o] = $sign;
                    }
                    $prevRateTmp[$o] = $rate;
                    $this->prevGradient[$l][$n] = $gradTmp;
                }
            }
        } // data
        // Получаем среднюю ошибку сети после каждого раунда
        $this->error = $this->mean($errors);
    }

}
