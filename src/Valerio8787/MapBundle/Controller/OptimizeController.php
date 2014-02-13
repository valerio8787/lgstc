<?php

namespace Valerio8787\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class OptimizeController extends Controller {

    //Менеджер сутностей
    private $em;

    /**
     * @Route("/optimize-route", name = "Optimize_Route")
     * @Template()
     */
    public function optimizeAction(Request $request) {
        if ($request->request->has('pos') && $request->request->has('algorithm')) {
            try {
                //Ініціалізація менеджера сутностей
                $this->em = $this->getDoctrine()->getManager();
                //вибірка точок та маршрутів між ними
                $poses = $this->em->getRepository('Valerio8787SchemaBundle:Pos')->createQueryBuilder('p')
                                ->where('p.id in (:poses)')
                                ->setParameter('poses', $request->request->get('pos'))
                                ->getQuery()->getArrayResult();
                shuffle($poses);

                return new JsonResponse(array(
                    'poses' => $poses,
                ));
//        $routes = $this->em->getRepository('Valerio8787SchemaBundle:PosToPos')->createQueryBuilder('ptp')
//                        ->select('ptp.distance, ptp.route, pf.id as pfrom, pt.id as pto')
//                        ->innerjoin('ptp.posFrom', 'pf')
//                        ->innerjoin('ptp.posTo', 'pt')
//                        ->where('pf.id in (310,311,312,313,314,315,316,317) AND pt.id in (310,311,312,313,314,315,316,317)')
//                        ->getQuery()->getArrayResult();
            } catch (\Exception $e) {
                return new Response('Error', 500);
            }
        } else {
            return new Response('400 Bad request', 400);
        }
    }

}
