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
                    if ($i = $j + 1) {
                        if ($this->best1($i, $j)) {
                            $this->swap($i, $j);
                        }
                    } else if (($i = 1) && ($j = $n)) {
                        if ($this->best1($i, $j)) {
                            $this->swap($i, $j);
                        }
                    } else
                    if ($this->best2($i, $j)) {
                        $this->swap($i, $j);
                    }
                }
            }
        } while ($change);
    }

    private function best1($i, $j) {
        return (($this->dm[$i - 1][$i] + $this->dm[$i][$j] + $this->dm[$j][$j + 1]) >
                ($this->dm[$i - 1][$j] + $this->dm[$j][$i] + $this->dm[$i][$j + 1]));
    }

    private function best2($i, $j) {
        return (($this->dm[$i - 1][$i] + $this->dm[$i][$i + 1] + $this->dm[$j - 1][$j] + $this->dm[$j][$j + 1]) >
                ($this->dm[$i - 1][$j] + $this->dm[$j][$i + 1] + $this->dm[$j - 1][$i] + $this->dm[$i][$j + 1]));
    }

    private function swap($i, $j) {
        
    }

    public function optimize() {
        $this->oneWay();
        $this->localOptimize();
        $this->resultPoints = array_merge($this->way, $this->pointsWithoutCoordinates);
    }

}
