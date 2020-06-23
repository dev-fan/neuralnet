<?php

declare(strict_types=1);

namespace NeuralNet\Layer;

/**
 * Softmax слой нейросети, применять с softmax нейроном
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2018 Gordon Freeman
 */
class Softmax extends Base
{

    /**
     * {@inheritDoc}
     */
    public function result(array $input): array
    {
        $output = [];
        foreach ($this as $n => $neuron) {
            /* @var $neuron \NeuralNet\Neuron\Softmax */
            $output[$n] = $neuron->result($input);
        }
        $summ = \array_sum($output);
        if ($summ) {
            foreach ($this as $n => $neuron) {
                $neuron->output /= $summ;
                $output[$n] = $neuron->output;
            }
        }
        return $output;
    }

}
