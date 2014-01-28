<?php

namespace Valerio8787\SchemaBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Valerio8787\SchemaBundle\Entity\Pos;
use Valerio8787\SchemaBundle\Entity\PosToPos;

class LoadRouteCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this
                ->setName('logistica:load-route')
                ->setDescription('Load routes for exists Poses');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $em = $this->getContainer()->get('doctrine')->getManager('default');
        $poses = $em->getRepository('Valerio8787SchemaBundle:Pos')->findAll();

        foreach ($poses as $pFrom) {
            var_dump($pFrom->getName());
            foreach ($poses as $pTo) {
                if ($pFrom->getId() != $pTo->getId()) {

                    $pft = new PosToPos();
                    $pft->setPosFrom($pFrom);
                    $pft->setPosTo($pTo);
                    $pft->setDistance(0);
                    $pft->setRoute($this->getRoute($pFrom, $pTo));
                    $em->persist($pft);
                }
                $em->flush();
            }
        }
    }

    private function getRoute($from, $to)
    {
        $url = 'http://maps.googleapis.com/maps/api/directions/json?origin=' . $from->getLatitude() . ',' . $from->getLongitude() . '&destination=' . $to->getLatitude() . ',' . $to->getLongitude() . '&sensor=false';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        //curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 25);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

}
