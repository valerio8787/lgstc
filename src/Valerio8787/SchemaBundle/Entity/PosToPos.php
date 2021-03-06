<?php

namespace Valerio8787\SchemaBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PosToPos
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class PosToPos
{
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(type="float")
     */
    private $distance;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     */
    private $route;
    
    /**
     * @var type
     * 
     * @ORM\ManyToOne(targetEntity="Pos")
     */
    protected $posFrom;
    
    /**
     * @var type
     * 
     * @ORM\ManyToOne(targetEntity="Pos")
     */
    protected $posTo;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set distance
     *
     * @param float $distance
     * @return PosToPos
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return float 
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return PosToPos
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string 
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set posFrom
     *
     * @param \Valerio8787\SchemaBundle\Entity\Pos $posFrom
     * @return PosToPos
     */
    public function setPosFrom(\Valerio8787\SchemaBundle\Entity\Pos $posFrom = null)
    {
        $this->posFrom = $posFrom;

        return $this;
    }

    /**
     * Get posFrom
     *
     * @return \Valerio8787\SchemaBundle\Entity\Pos 
     */
    public function getPosFrom()
    {
        return $this->posFrom;
    }

    /**
     * Set posTo
     *
     * @param \Valerio8787\SchemaBundle\Entity\Pos $posTo
     * @return PosToPos
     */
    public function setPosTo(\Valerio8787\SchemaBundle\Entity\Pos $posTo = null)
    {
        $this->posTo = $posTo;

        return $this;
    }

    /**
     * Get posTo
     *
     * @return \Valerio8787\SchemaBundle\Entity\Pos 
     */
    public function getPosTo()
    {
        return $this->posTo;
    }
}
