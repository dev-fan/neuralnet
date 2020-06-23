<?php

declare(strict_types=1);

namespace NeuralNet\Helper;

/**
 * Работа с данными (нормализация, среднее, sse)
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2018 Gordon Freeman
 */
class Data
{

    use \Math\Deviation;
    use \Math\Variance;
    use \Math\Mean;
    use \Math\Sse;

    /**
     * Базовая нормализация данных
     *
     * @param array $data
     * @return array
     */
    public function normalize(array $data): array
    {
        $deviation = $this->deviation($data);
        $mean = $this->mean($data);
        $result = [];
        foreach ($data as $i => $val) {
            $result[$i] = ($val - $mean) / $deviation;
        }
        return $result;
    }

}
