<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Question;
use App\Entity\User;
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
            KernelEvents::VIEW => ['setCreatedByOnQuestionCreation', EventPriorities::PRE_WRITE],
        ];
    }

    public function setCreatedByOnQuestionCreation(ViewEvent $event): void
    {
        $question = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        // Only proceed if it's a Question object and a POST request
        if ($question instanceof Question && $method === 'POST') {
            $user = $this->security->getUser();
            Assert::isInstanceOf($user, User::class);

            $question->setCreatedBy($user);
        }
    }
}
