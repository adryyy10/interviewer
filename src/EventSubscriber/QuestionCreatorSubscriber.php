<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Question;
use App\Entity\Questionnaire;
use App\Entity\User;
use App\Interface\CreatableByUserInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Webmozart\Assert\Assert;

class QuestionCreationSubscriber implements EventSubscriberInterface
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
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

        // If we are creating a new user or method !== POST, return
        if ($method !== 'POST' || str_contains($event->getRequest()->getUri(), '/signup')) {
            return;
        }

        $user = $this->security->getUser();
        Assert::isInstanceOf($user, User::class);

        if ($entity instanceof CreatableByUserInterface) {
            $entity->setCreatedBy($user);
        }
    }
}
