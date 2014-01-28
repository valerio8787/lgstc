<?php

namespace Valerio8787\SchemaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{

    public function getRoutesForDay(Request $request)
    {

        if ($request->request->has('dayOfRoute') && $request->request->has('employee')) {
            if ($request->request->get('dayOfRoute') && $request->request->get('employee')) {
                try {
                    $config = new \Doctrine\DBAL\Configuration();
                    $connectionParams = \Nitra\CommonBundle\ConnectionHelper\ConnectionHelper::connectToCRM_HData();
                    $conn = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
                    //Торговые точки, которые по плану должен посетить ЭТА на определенную дату
                    $routeDetails = $conn->fetchAll(
                            'SELECT OL_Number, OL_id FROM DailyRoutes rd WHERE employee_id=:id AND Date=:date ORDER BY OL_number', array(
                        'id' => $request->request->get('employee'),
                        'date' => $request->request->get('dayOfRoute')));
                    //массив pos_id
                    $plans_id = array();
                    foreach ($routeDetails as $rd) {
                        $plans_id[] = $rd['OL_id'];
                    }
                    //получение значений широты и долготы ТП
                    $poses = $this->em->getRepository('NitraSchemaCDBBundle:POS')
                            ->createQueryBuilder('p')
                            ->select('p.id, p.longitude, p.latitude')
                            ->where('p.id IN (:rd)')
                            ->setParameter('rd', $plans_id)
                            ->getQuery()
                            ->getResult();
                    $result = array();
//                    $result[] = array(
//                        'id' => $rr['OL_id'],
//                        'OL_number' => $rr['OL_Number'],
//                        'latitude' => $p['latitude'],
//                        'longitude' => $p['longitude']
//                    );
                    $conn->close();
                    //Торговые точки, которые посетил ЭТА 
                    $visits = $this->em->getRepository('NitraSchemaCDBBundle:Visit')
                            ->createQueryBuilder('v')
                            ->select('v.dateFinish, v.latitude, v.longitude')
                            ->innerJoin('v.pos', 'p')
                            ->innerJoin('p.employee', 'e')
                            ->where('v.dateFinish=:dateFinish AND e.id=:employee')
                            ->setParameters(array(
                                'dateFinish' => $request->request->get('dayOfRoute'),
                                'employee' => $request->request->get('employee')
                            ))
                            ->getQuery()
                            ->getArrayResult();
                    return new JsonResponse(array(
                        'visits' => $visits,
                        'result' => $result));
                } catch (\Exception $e) {
                    if (!is_null($conn)) {
                        $conn->close();
                    }
                    return new Response('Error', 200);
                }
            }
        }
        return new Response('Bad Request', 200);
    }

}
