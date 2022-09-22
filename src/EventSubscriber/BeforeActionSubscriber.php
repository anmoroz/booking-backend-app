<?php
/*
 * This file is part of the Reservation application project.
 *
 * https://github.com/anmoroz
 */

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use JsonException;

class BeforeActionSubscriber implements EventSubscriberInterface
{
    public function onRequestEvent(RequestEvent $event)
    {
        $request = $event->getRequest();

        if ($request->getContentType() === 'json' && $request->getContent()) {
            try {
                $data = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
                $request->request->replace(is_array($data) ? $data : []);
            } catch (JsonException) {
                throw new BadRequestHttpException('Invalid json format');
            }
        }
    }

    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => 'onRequestEvent',
        ];
    }
}