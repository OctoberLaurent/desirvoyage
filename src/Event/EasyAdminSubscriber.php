<?php

namespace App\Event;

use App\Entity\Travel;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class EasyAdminSubscriber implements EventSubscriberInterface
{
    #[\Override]
    public static function getSubscribedEvents(): array
    {
        return [
            'easy_admin.pre_persist' => ['onPreUpdate'],
        ];
    }

    /**
     * @param GenericEvent<Travel> $event
     */
    public function onPreUpdate(GenericEvent $event): void
    {
        $entity = $event->getSubject();
        if (!$entity instanceof Travel) {
            return;
        }

        // Persist pictures
        foreach ($entity->getPictures() as $pict) {
            $entity->addPicture($pict);
        }

        // Persist stay
        foreach ($entity->getStays() as $stay) {
            $entity->addStay($stay);
        }

        // Persist option
        foreach ($entity->getOptions() as $option) {
            $entity->addOptions($option);
        }

        $event['entity'] = $entity;
    }
}
