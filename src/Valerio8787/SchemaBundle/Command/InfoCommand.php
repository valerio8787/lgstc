<?php

namespace Valerio8787\SchemaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InfoCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
                ->setName('logistica:info-route')
                ->setDescription('Load routes for exists Poses');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $em = $this->getContainer()->get('doctrine')->getManager('default');
        $p = $em->getRepository('Valerio8787SchemaBundle:Pos')->find(413);
        $ptpes = $em->getRepository('Valerio8787SchemaBundle:PosToPos')->createQueryBuilder('q')
                        ->where('q.posFrom = :p OR q.posTo = :p')
                        ->setParameter('p', $p)->getQuery()->getResult();

        foreach ($ptpes as $ptp) {
            $rInfo = json_decode($ptp->getRoute());
            if ($rInfo) {
                $ptp->setDistance($rInfo->routes[0]->legs[0]->distance->value);
                $steps = array();
                foreach ($rInfo->routes[0]->legs[0]->steps as $step) {
                    $steps[] = array(
                        'start' => array('lat' => $step->start_location->lat, 'lng' => $step->start_location->lng),
                        'end' => array('lat' => $step->end_location->lat, 'lng' => $step->end_location->lng),
                    );
                }
                $ptp->setRoute(json_encode($steps));
            }
        }
        $em->flush();
    }

}
