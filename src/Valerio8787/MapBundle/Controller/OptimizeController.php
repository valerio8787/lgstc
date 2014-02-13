<?php

namespace Valerio8787\MapBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Valerio8787\OptimizeBundle\Optimizer\TravelingSalesmanOptimizer;
use Valerio8787\OptimizeBundle\Optimizer\PetalClockwiseOptimizer;
use Valerio8787\OptimizeBundle\Optimizer\PetalCounterClockwiseOptimizer;

class OptimizeController extends Controller
{

    //Менеджер сутностей
    private $em;

    /**
     * @Route("/optimize-route", name = "Optimize_Route")
     * @Template()
     */
    public function optimizeAction(Request $request)
    {
        if ($request->request->has('pos') && $request->request->has('algorithm')) {
            try {
                //Ініціалізація менеджера сутностей
                $this->em = $this->getDoctrine()->getManager();
                //вибірка точок та маршрутів між ними
                $poses = $this->em->getRepository('Valerio8787SchemaBundle:Pos')->createQueryBuilder('p')
                                ->select('p.id, p.name,p.address, p.latitude as lat, p.longitude as lng')
                                ->where('p.id in (:poses)')
                                ->setParameter('poses', $request->request->get('pos'))
                                ->getQuery()->getArrayResult();
                $optimizer = $this->getOptimizer($poses, $request->request->get('algorithm'));
                return new JsonResponse(array(
                    'poses' => $optimizer->getOptimazeResult(),
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

    private function getOptimizer($points, $algorithm)
    {

        switch ($algorithm) {
            case 'CW':
                $optimizer = new PetalClockwiseOptimizer($points);
            //break;
            case 'CCW':
                $optimizer = new PetalCounterClockwiseOptimizer(($points));
                break;
            case 'TS':
                //todo find distance matrix
                $dm = array();
                $optimizer = new TravelingSalesmanOptimizer($points, $dm);
                break;
            default:
                $optimizer = null;
                break;
        }
        return $optimizer;
    }

}
