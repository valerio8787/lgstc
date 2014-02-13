<?php

namespace Valerio8787\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class IndexMapController extends Controller
{

    //Менеджер сутностей
    private $em;

    /**
     * @Route("/index")
     * @Template()
     */
    public function indexAction()
    {
        //Ініціалізація менеджера сутностей
        $this->em = $this->getDoctrine()->getManager();

        //вибірка точок та маршрутів між ними
        $poses = $this->em->getRepository('Valerio8787SchemaBundle:Pos')->createQueryBuilder('p')
                        ->select('p.id, p.name,p.address, p.latitude as lat, p.longitude as lng')
                        ->where('p.id in (310,311,312,313,314,315,316,317)')
                        ->getQuery()->getArrayResult();
        $routes = $this->em->getRepository('Valerio8787SchemaBundle:PosToPos')->createQueryBuilder('ptp')
                        ->select('ptp.distance, ptp.route, pf.id as pfrom, pt.id as pto')
                        ->innerjoin('ptp.posFrom', 'pf')
                        ->innerjoin('ptp.posTo', 'pt')
                        ->where('pf.id in (310,311,312,313,314,315,316,317) AND pt.id in (310,311,312,313,314,315,316,317)')
                        ->getQuery()->getArrayResult();

        return(array('poses' => json_encode($poses),
            'routes' => json_encode($routes),
            'posesData' => $poses,));
    }

}
