<?php

namespace Valerio8787\OptimizeBundle\Optimizer;

/**
 * Класс для пошуку оптимального шляху між ТП
 * Задача Комівояжера 
 * метод локальної оптимізації
 */
class TravelingSalesmanOptimizer extends AbstractOptimizer {

//матриця відстаней
    private $dm;
    private $way = array();

    public function __construct($points, $distanceMatrix) {
        parent::__construct($points);
        $this->dm = $distanceMatrix;
    }

    private function oneWay() {
        $i = 0;
        foreach ($this->points as $p) {
            $this->way[] = array('point' => $p, 'ind' => $i++);
        }
    }

    private function localOptimize() {
        $n = count($this->way);
        do {
            $change = false;
            for ($i = 0; $i < $n - 1; $i++) {
                for ($j = $i + 1; $j < $n; $i++) {
                    $change = true;
                }
            }
        } while ($change);
    }

    protected function optimize() {
        $this->oneWay();
        $this->localOptimize();
        $this->resultPoints = array_merge($this->way, $this->pointsWithoutCoordinates);
    }

}
