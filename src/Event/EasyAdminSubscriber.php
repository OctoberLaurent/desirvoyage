<?php

namespace App\Event;

use App\Entity\Travel;
use App\Service\MakeSerialService;
use App\Repository\StaysRepository;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents()
    {
        return [
            'easy_admin.pre_persist' => array('onPreUpdate'),
        ];
    }

    public function onPreUpdate(GenericEvent $event)
    {
        $entity = $event->getSubject();
        if (!($entity instanceof Travel)) {
            return;
        }

        // Persist pictures
        foreach($event->getSubject()->getPictures() as $pict){
            $entity->addPicture($pict);
        }

        // Persist stay
        foreach($event->getSubject()->getStays() as $stay){
            $entity->addStay($stay);
        }

        // Persist option
        foreach($event->getSubject()->getOptions() as $option){
            $entity->addOptions($option);
        }

        $event['entity'] = $entity;
    }
}
