<?php

namespace Valerio8787\OptimizeBundle\Optimizer;

class AbstractOptimizer {

    private $points = array();
    private $resultPoints = array();
    private $pointsWithoutCoordinates = array();

    public function __construct($points) {
        $this->clearPoints($points);
    }

    final protected function clearPoints($points) {
        foreach ($points as $point) {
            if (($point['lat'] && $point['lng']) && ($point['lat'] != 0.0 && $point['lng'] != 0.0)) {
                $this->points[] = $point;
            } else {
                $this->pointsWithoutCoordinates[] = $point;
            }
        }
    }

    protected function optimize();
    final public function getOptimazeResult()
    {
        $this->optimize();
        return $this->resultPoints;
    }
}
