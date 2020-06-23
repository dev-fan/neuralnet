<?php

declare(strict_types=1);

namespace NeuralNet\Train;

/**
 * Алгоритм сравнительного разногласия (CD-k)
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2017 Gordon Freeman
 */
class ContrastiveDivergence extends Base
{

    /**
     * {@inheritDoc}
     */
    protected function epoch(Data\Base $data): void
    {
        $rate = $this->config->rate;
        $errors = [];
        $layers = $this->nn->getLayers();
        $lastLayer = \end($layers);
        foreach ($data as list($input, $expect)) {
            $current = $this->nn->result($input);
            $errors[] = $this->lossFunc->loss($current, $expect);
            // Сэмплирования (Gibbs sampling)
            for ($i = 0; $i < $this->config->sampling; ++$i) {
                $current = $this->nn->result($this->backward($current));
            }
            // Calc correction & update
            foreach ($expect as $n => $exp) {
                // Первое слагаемое называется положительной фазой, а второе
                // со знаком минус называется отрицательной фазой.
                $fix = ($exp - $current[$n]) * $rate;
                foreach ($input as $w => $inp) {
                    $lastLayer[$n]->weight[$w] += $inp * $fix;
                }
                $lastLayer[$n]->bias += $fix;
            }
        }
        // Получаем среднюю ошибку сети после каждого раунда
        $this->error = $this->mean($errors);
    }

    /**
     * Обратный проход
     *
     * @param array $input Входные данные
     * @return array
     */
    protected function backward(array $input): array
    {
        $out = [];
        foreach ($this->nnReverse as $neurons) {
            $sum = \array_fill(0, \count($neurons[0]->weight), 0);
            foreach ($neurons as $n => $neuron) {
                foreach ($neuron->weight as $w => $weight) {
                    $sum[$w] += $weight * $input[$n];
                }
            }
            foreach ($sum as $w => $s) {
                $out[$w] = $neuron->activation($s);
            }
            $input = $out;
        }
        return $out;
    }

}
