<?php

namespace Valerio8787\OptimizeBundle\Optimizer;

/**
 * Класс для пошуку оптимального шляху між ТП
 * Метод пелюстки за годинниковою стрілкою
 * з використанням координат ТП (широти(latitude) и довготи(longitude))
 */
class PetalClockwiseOptimizer extends AbstractOptimizer {

    private $latitudeMax;
    private $longitudeMax;
    private $latitudeMin;
    private $longitudeMin;
    private $firstQrPoints = array();
    private $secondQrPoints = array();
    private $thirdQrPoints = array();
    private $fourthQrPoints = array();
    private $latitudeMediana;
    private $longitudeMediana;
    private $firstPoint;
    private $qrFirstPoint;

    public function __construct($points) {
        parent::__construct($points);
        $this->latitudeMax = -90.0;
        $this->longitudeMax = -180.0;
        $this->latitudeMin = 90.0;
        $this->longitudeMin = 180.0;
    }

    /**
     *  Поиск экстремумов
     */
    protected function findExtremums() {
        foreach ($this->points as $point) {
            if ($point['lat'] > $this->latitudeMax) {
                $this->latitudeMax = $point['lat'];
            }
            if ($point['lat'] < $this->latitudeMin) {
                $this->latitudeMin = $point['lat'];
            }
            if ($point['lng'] > $this->longitudeMax) {
                $this->longitudeMax = $point['lng'];
            }
            if ($point['lng'] < $this->longitudeMin) {
                $this->longitudeMin = $point['lng'];
            }
        }
    }

    /**
     *  Поис медиан
     */
    protected function findMediana() {
        $this->latitudeMediana = ($this->latitudeMax + $this->latitudeMin) / 2;
        $this->longitudeMediana = ($this->longitudeMax + $this->longitudeMin) / 2;
    }

    /**
     * Установка первой точки в четверть
     * Распределение всех точек по четвертям 
     */
    protected function establishPoints() {
        if (count($this->points) > 0) {
            $this->setQrFirstPoint($this->points[0]);
        }
        foreach ($this->points as $point) {
            if (($point['lat'] >= $this->latitudeMediana) && ($point['lng'] >= $this->longitudeMediana)) {
                $this->firstQrPoints[] = $point;
            }
            if (($point['lat'] > $this->latitudeMediana) && ($point['lng'] < $this->longitudeMediana)) {
                $this->secondQrPoints[] = $point;
            }
            if (($point['lat'] <= $this->latitudeMediana) && ($point['lng'] <= $this->longitudeMediana)) {
                $this->thirdQrPoints[] = $point;
            }
            if (($point['lat'] < $this->latitudeMediana) && ($point['lng'] > $this->longitudeMediana)) {
                $this->fourthQrPoints[] = $point;
            }
        }
    }

    /**
     * Поиск четветри в которой находится первая точка 
     */
    protected function setQrFirstPoint($point) {
        $this->firstPoint = $point;
        if (($point['lat'] >= $this->latitudeMediana) && ($point['lng'] >= $this->longitudeMediana)) {
            $this->qrFirstPoint = 1;
        }
        if (($point['lat'] > $this->latitudeMediana) && ($point['lng'] < $this->longitudeMediana)) {
            $this->qrFirstPoint = 2;
        }
        if (($point['lat'] <= $this->latitudeMediana) && ($point['lng'] <= $this->longitudeMediana)) {
            $this->qrFirstPoint = 3;
        }
        if (($point['lat'] < $this->latitudeMediana) && ($point['lng'] > $this->longitudeMediana)) {
            $this->qrFirstPoint = 4;
        }
    }

    /**
     * Сортировка точек для отрисовки по четвертям
     * первая четверть отрисовуется вверх (от первой ТТ до ТТ с максивальным значение широты и долготы)
     */
    protected function getFirstQuarter() {
        usort($this->firstQrPoints, function($a, $b) {
            if ($a['lat'] == $b['lat']) {
                return 0;
            }
            return ($a['lat'] < $b['lat'] ? 1 : -1);
        });
        return $this->firstQrPoints;
    }

    /**
     * Сортировка точек для отрисовки по четвертям
     * втораячетверть отрисовуется вниз(от первой ТТ до ТТ с минимальным значение широты и долготы)
     */
    protected function getSecondQuarter() {
        usort($this->secondQrPoints, function($a, $b) {
            if ($a['lat'] == $b['lat']) {
                return 0;
            }
            return ($a['lat'] < $b['lat'] ? -1 : 1);
        });
        return $this->secondQrPoints;
    }

    /**
     * Сортировка точек для отрисовки по четвертям
     * третья четверть отрисовуется вверх (от первой ТТ до ТТ с максивальным значение широты и долготы)
     */
    protected function getThirdQuarter() {
        usort($this->thirdQrPoints, function($a, $b) {
            if ($a['lat'] == $b['lat']) {
                return 0;
            }
            return ($a['lat'] < $b['lat'] ? -1 : 1 );
        });
        return $this->thirdQrPoints;
    }

    /**
     * Сортировка точек для отрисовки по четвертям 
     * четвертая четверть отрисовуется вверх (от первой ТТ до ТТ с минимальным значение широты и долготы)
     */
    protected function getFourthQuarter() {
        usort($this->fourthQrPoints, function($a, $b) {
            if ($a['lat'] == $b['lat']) {
                return 0;
            }
            return ($a['lat'] < $b['lat'] ? 1 : -1);
        });
        return $this->fourthQrPoints;
    }

    /**
     * Поиск точек следующих после точки включая ее 
     */
    protected function findSecPoint($points) {
        $resultArray = array();
        $flagAfter = false;
        foreach ($points as $point) {
            if (($point['lat'] == $this->firstPoint['lat']) && ($point['lng'] == $this->firstPoint['lng'])) {
                $flagAfter = true;
                $resultArray[] = $point;
                continue;
            }
            if ($flagAfter) {
                $resultArray[] = $point;
            }
        }
        return $resultArray;
    }

    /**
     * Поиск точек находящихся перед точкой в ее четверти
     */
    protected function findPrePoint($points) {
        $resultArray = array();
        foreach ($points as $point) {
            if (($point['lat'] == $this->firstPoint['lat']) && ($point['lng'] == $this->firstPoint['lng'])) {
                return $resultArray;
            }
            $resultArray[] = $point;
        }
        return $resultArray;
    }

    /**
     * 
     * Функция формирования результирующего массива точек 
     */
    protected function optimize() {
        $this->findExtremums();
        $this->findMediana();
        $this->establishPoints();
        $optimizeRoute = array();
        switch ($this->qrFirstPoint) {
            case 1:
                $this->getFirstQuarter();
                $optimizeRoute = array_merge($this->findSecPoint($this->firstQrPoints), $this->getFourthQuarter(), $this->getThirdQuarter(), $this->getSecondQuarter(), $this->findPrePoint($this->firstQrPoints));
                break;
            case 2:
                $this->getSecondQuarter();
                $optimizeRoute = array_merge($this->findSecPoint($this->secondQrPoints), $this->getFirstQuarter(), $this->getFourthQuarter(), $this->getThirdQuarter(), $this->findPrePoint($this->secondQrPoints));
                break;
            case 3:
                $this->getThirdQuarter();
                $optimizeRoute = array_merge($this->findSecPoint($this->thirdQrPoints), $this->getSecondQuarter(), $this->getFirstQuarter(), $this->getFourthQuarter(), $this->findPrePoint($this->thirdQrPoints));
                break;
            case 4:
                $this->getFourthQuarter();
                $optimizeRoute = array_merge($this->findSecPoint($this->fourthQrPoints), $this->getThirdQuarter(), $this->getSecondQuarter(), $this->getFirstQuarter(), $this->findPrePoint($this->fourthQrPoints));
                break;
        }
        $forSortArray = array_merge($optimizeRoute, $this->pointsWithoutCoordinates);
        $i = 1;
        foreach ($forSortArray as $key => $element) {
            $forSortArray[$key]['por'] = $i++;
        }
        $this->resultPoints = $forSortArray;
    }

}
