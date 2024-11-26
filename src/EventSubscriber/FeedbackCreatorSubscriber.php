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

class FeedbackCreatorSubscriber implements EventSubscriberInterface
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

        // If method !== POST or we are creating a user, skip
        if ('POST' !== $method || str_contains($event->getRequest()->getUri(), '/signup')) {
            return;
        }

        $user = $this->security->getUser();
        Assert::isInstanceOf($user, User::class);

        if ($entity instanceof CreatableByUserInterface) {
            $entity->setCreatedBy($user);
        }
    }
}
