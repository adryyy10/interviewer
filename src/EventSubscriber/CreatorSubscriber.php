<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\User;
use App\Interface\CreatableByUserInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webmozart\Assert\Assert;

class CreatorSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly Security $security)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setCreator', EventPriorities::PRE_WRITE],
        ];
    }

    public function setCreator(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        if ('POST' !== $method) {
            return;
        }

        // Return if we are creating a new user
        if (str_contains($event->getRequest()->getUri(), '/signup')) {
            return;
        }

        $user = $this->security->getUser();
        Assert::isInstanceOf($user, User::class);

        if ($entity instanceof CreatableByUserInterface) {
            $entity->setCreatedBy($user);
        }
    }
}
