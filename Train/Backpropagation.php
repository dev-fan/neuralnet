<?php

declare(strict_types=1);

namespace NeuralNet\Train;

/**
 * Обучение методом обратного распространения ошибки.
 *
 * x ::: Входной вектор обучающих данных x = (x[1], x[2], ..., x[i], ..., x[n])
 * t ::: Вектор целевых выходных значений, предоставляемых учителем t = (t[1], t[2], ..., t[i], ...,t[m])
 * e[k] ::: Составляющая корректировки весов связей w[jk], соответствующая
 *     ошибке выходного нейрона Yk; также, информация об ошибке нейрона Yk,
 *     которая распространяется  тем нейронам скрытого слоя, которые связаны с Yk.
 * e[j] ::: Составляющая корректировки весов связей v[ij], соответствующая
 *     распространяемой от выходного слоя к скрытому нейрону Zj информации об ошибке.
 * a ::: Скорость обучения.
 * X[i] ::: Нейрон на входе с индексом i. Для входных нейронов входной и
 *     выходной сигналы одинаковы — x[i].
 * v[0j] ::: Смещение скрытого нейрона j.
 * Z[j] ::: Скрытый нейрон j; Суммарное значение подаваемое на вход скрытого
 *     элемента Z[j] обозначается z_in[j]: z_in[j] = v[0j] + SUM[i..n](x[i] * v[ij])
 *     Сигнал на выходе Z[j] (результат применения к z_in[j] активационной функции)
 *     обозначается z[j]: z[j] = f(z_in[j])
 * w[0k] ::: Смещение нейрона на выходе.
 * Y[k] ::: Нейрон на выходе под индексом k; Суммарное значение подаваемое
 *     на вход выходного элемента Y[k] обозначается y_in[k]: y_in[k] = w[0k] + SUM[j..m](z[j] * w[jk]).
 *     Сигнал на выходе Y[k] (результат применения к y_in[k] активационной функции) обозначается y[k]
 *
 * https://ru.wikipedia.org/wiki/%D0%9C%D0%B5%D1%82%D0%BE%D0%B4_%D0%BE%D0%B1%D1%80%D0%B0%D1%82%D0%BD%D0%BE%D0%B3%D0%BE_%D1%80%D0%B0%D1%81%D0%BF%D1%80%D0%BE%D1%81%D1%82%D1%80%D0%B0%D0%BD%D0%B5%D0%BD%D0%B8%D1%8F_%D0%BE%D1%88%D0%B8%D0%B1%D0%BA%D0%B8
 * https://habrahabr.ru/post/198268/
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2016 Gordon Freeman
 */
class Backpropagation extends Base
{

    /**
     * @var int Индекс выходного слоя сети
     */
    protected $idxOutlayer;

    /**
     * @var array Коррекция весов предыдущего шага
     */
    protected $prevWeightCorrection = [];

    /**
     * {@inheritDoc}
     */
    public function __construct(\NeuralNet\Train\Config\Backpropagation $config, \NeuralNet\Train\LossFunc\Base $lossFunc, \NeuralNet\Net\FeedForward $nn)
    {
        parent::__construct($config, $lossFunc, $nn);
        $this->idxOutlayer = \count($this->nn->getLayers()) - 1;
        foreach ($this->nn->getLayers() as $l => $layer) {
            foreach ($layer as $n => $neuron) {
                $this->prevWeightCorrection[$l][$n] = \array_fill(0, \count($neuron->weight), 0);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function epoch($data): void
    {
        // Step 2. Для каждой пары {данные, целевое значение} выполняются шаги 3 — 8.
        $errors = [];
        foreach ($data as list($input, $expect)) {
            // Step 3 и Step 5. Описание в $nn->result(), Step 4 Описание в $layer->result()
            $actual = $this->nn->result($input);
            $errors[] = $this->lossFunc->loss($actual, $expect);
            $this->backpropagate($actual, $expect);
        }
        // Получаем среднюю ошибку сети после каждого раунда
        $this->error = $this->mean($errors);
    }

    /**
     * Реализация обновления весов с учетом целевой функции
     *
     * @param array $actual Предсказанные значения
     * @param array $expect Ожидаемые значения
     */
    protected function backpropagate(array $actual, array $expect)
    {
        $rate = $this->config->rate;
        $momentum = $this->config->momentum;
        foreach ($this->nnReverse as $l => $layer) {
            foreach ($layer as $n => $neuron) {
                /* @var $neuron \NeuralNet\Neuron\Base */
                if ($l == $this->idxOutlayer) {
                    $this->lossFunc->dCdSo($l, $n, $neuron, $actual[$n], $expect[$n]);
                } else {
                    $this->lossFunc->dCdSh($l, $n, $neuron, $this->nn->getLayer($l + 1));
                }
            }
        }
        // Step 8. Изменение весов.
        // Каждый выходной нейрон (Y[k], k=1,2,..,m) изменяет веса своих связей с
        // элементом смещения и скрытыми нейронами: w[jk](new) = w[jk](old) - Dw[jk]
        // Каждый скрытый нейрон (Z[j], j=1,2,..,p) изменяет веса своих связей с
        // элементом смещения и выходными нейронами: v[ij](new) = v[ij](old) - Dv[ij]
        foreach ($this->nn->getLayers() as $l => $layer) {
            foreach ($layer as $n => $neuron) {
                $fix = $rate * $this->lossFunc->dCdS($l, $n);
                $prevTmp = $this->prevWeightCorrection[$l][$n];
                foreach ($neuron->weight as $w => $weight) {
                    $neuron->weight[$w] -= ($prevTmp[$w] = $fix * $neuron->input[$w] + $momentum * $prevTmp[$w]);
                }
                $this->prevWeightCorrection[$l][$n] = $prevTmp;
                // Изменение смещения
                $neuron->bias -= $fix;
            }
        }
    }

}
