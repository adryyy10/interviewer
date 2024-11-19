<?php

namespace App\EventSubscriber;

use ApiPlatform\Symfony\EventListener\EventPriorities;
use App\Entity\Quiz;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class QuizUserAnswerSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['prePersist', EventPriorities::PRE_WRITE],
        ];
    }

    public function prePersist(ViewEvent $event): void
    {
        $entity = $event->getControllerResult();

        if (!$entity instanceof Quiz) {
            return;
        }

        foreach ($entity->getUserAnswers() as $userAnswer) {
            $userAnswer->setQuiz($entity);
        }
    }
}
