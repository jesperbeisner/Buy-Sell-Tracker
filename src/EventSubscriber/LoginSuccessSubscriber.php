<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\User;
use App\Notifier\DiscordNotifier;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSuccessSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private DiscordNotifier $discordNotifier,
    ) {}

    public function onLoginSuccessEvent(LoginSuccessEvent $event): void
    {
        /** @var User $user */
        $user = $event->getUser();

        $this->discordNotifier->send(
            "User login",
            "The user '{$user->getUsername()}' has successfully logged in",
            DiscordNotifier::COLOR_GREEN
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccessEvent',
        ];
    }
}
