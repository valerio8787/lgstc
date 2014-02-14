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
        $k = 0;
        do {
            $k++;
            $change = false;
            for ($i = 1; $i < $n - 2; $i++) {
                for ($j = $i + 1; $j < $n - 1; $j++) {
                    // var_dump($i,$j);
                    if ($j == $i + 1) {
                        if ($this->best1($i, $j)) {
                            $this->swap($i, $j);
                            $change = true;
                        }
                    } else if (($i == 1) && ($j == $n)) {
                        if ($this->best1($i, $j)) {
                            $this->swap($i, $j);
                            $change = true;
                        }
                    } else
                    if ($this->best2($i, $j)) {
                        $this->swap($i, $j);
                        $change = true;
                    }
                }
            }
        } while ($change);
    }

    private function best1($i, $j) {

        //$this->way[$i]['point']['id'], $this->way[$j]['point']['id']
        return (($this->dm[$this->way[$i - 1]['point']['id']][$this->way[$i]['point']['id']] + $this->dm[$this->way[$i]['point']['id']][$this->way[$j]['point']['id']] + $this->dm[$this->way[$j]['point']['id']][$this->way[$j + 1]['point']['id']]) >
                ($this->dm[$this->way[$i - 1]['point']['id']][$this->way[$j]['point']['id']] + $this->dm[$this->way[$j]['point']['id']][$this->way[$i]['point']['id']] + $this->dm[$this->way[$i]['point']['id']][$this->way[$j + 1]['point']['id']]));
    }

    private function best2($i, $j) {
        return (($this->dm[$this->way[$i - 1]['point']['id']][$this->way[$i]['point']['id']] + $this->dm[$this->way[$i]['point']['id']][$this->way[$i + 1]['point']['id']] + $this->dm[$this->way[$j - 1]['point']['id']][$this->way[$j]['point']['id']] + $this->dm[$this->way[$j]['point']['id']][$this->way[$j + 1]['point']['id']]) >
                ($this->dm[$this->way[$i - 1]['point']['id']][$this->way[$j]['point']['id']] + $this->dm[$this->way[$j]['point']['id']][$this->way[$i + 1]['point']['id']] + $this->dm[$this->way[$j - 1]['point']['id']][$this->way[$i]['point']['id']] + $this->dm[$this->way[$i]['point']['id']][$this->way[$j + 1]['point']['id']]));
    }

    private function swap($i, $j) {
        $tmp = $this->way[$i];
        $this->way[$i] = $this->way[$j];
        $this->way[$j] = $tmp;
    }

    public function optimize() {
        $this->oneWay();
        $this->localOptimize();
        $resultPoints = array();
        foreach ($this->way as $p) {
            $resultPoints[] = $p['point'];
        }
        $this->resultPoints = array_merge($resultPoints, $this->pointsWithoutCoordinates);
    }

}
