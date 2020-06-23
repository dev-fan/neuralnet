<?php

declare(strict_types=1);

namespace NeuralNet\Net;

/**
 * Наивный байесовский классификатор
 * https://habr.com/ru/post/184574/
 * https://habr.com/ru/company/otus/blog/473468/
 * Небольшое введение в теорию вероятностей. Выражение P() используется для обозначения
 * вероятности. Например, P(А) = 0.2 означает, что событие А произойдет с вероятностью 20%.
 * Выражения, типа P(А|B) используются для обозначения условных вероятностей.
 * Например, P(А|B) = 0.3 означает, что вероятность события А, при условии, что
 * случилось событие В, составляет 30%. Совместная вероятность P(А, B) используется
 * для обозначения вероятности того, что события А и В произойдут одновременно.
 * Если события А и В независимы, то P(А, B) = P(А) * P(B). Если события А и В
 * зависимы, то P(А, B) = P(А) * P(B|A).
 *
 * Наивный байесовский алгоритм – это алгоритм классификации, основанный на
 * теореме Байеса с допущением о независимости признаков. Другими словами, НБА
 * предполагает, что наличие какого-либо признака в классе не связано с
 * наличием какого-либо другого признака.
 *
 * В основе NBC (Naive Bayes Classifier) лежит теорема Байеса:
 * P(A|B) = P(B|A) * P(A) / P(B)
 *   P(A|B) - вероятность наступления события А, при условии, что событие В уже случилось;
 *   P(B|A) - вероятность наступления события В, при условии, что событие А уже случилось;
 *   P(A) - априорная (безусловная) вероятность наступления события А;
 *   P(B) - априорная (безусловная) вероятность наступления события В.
 *
 * @author      Gordon Freeman <toxa82@gmail.com>
 * @copyright   Copyright (c) 2019 Gordon Freeman
 */
class NaiveBayes extends Base
{

    /**
     * @var array Общий счетчик определяемых типов [type]=int
     */
    protected $types = [];

    /**
     * @var array Счетчик параметров по типам [type][param]=int
     */
    protected $params = [];

    /**
     * @inheritDoc
     */
    public function result(array $params): array
    {
        $bestType = null;
        $bestLikelihood = 0;
        $totalCnt = \array_sum($this->types) + 1;
        foreach ($this->types as $type => $typeCnt) {
            // Считаем вероятность P(Type)
            $likelihood = ($typeCnt + 1) / $totalCnt;
            $typeParams = $this->params[$type];
            $paramCnt = \array_sum($typeParams) + 1;
            foreach ($params as $param) {
                // Считаем вероятность P(Param, Type) / Multinomial bayes model со сглаживание Лапласа
                $likelihood *= (($typeParams[$param] ?? 0) + 1) / $paramCnt;
            }
            if ($likelihood > $bestLikelihood) {
                $bestLikelihood = $likelihood;
                $bestType = $type;
            }
        }
        return [$bestType];
    }

    public function train(array $statements, $type)
    {
        if (!isset($this->types[$type])) {
            $this->types[$type] = 0;
        }
        foreach ($statements as $param) {
            if (!isset($this->params[$type][$param])) {
                $this->params[$type][$param] = 0;
            }
            $this->params[$type][$param]++; // increment the param count for the type
        }
        $this->types[$type]++; // increment the document count for the type
    }

    /**
     * Список свойств для сериализации
     *
     * @return array
     */
    public function __sleep(): array
    {
        return ['types', 'params'];
    }

}
