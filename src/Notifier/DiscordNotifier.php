<?php

declare(strict_types=1);

namespace App\Notifier;

use App\Entity\User;
use DateTime;
use Exception;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DiscordNotifier
{
    // https://www.spycolor.com/
    public const COLOR_BLACK = 0;
    public const COLOR_WHITE = 16777215;
    public const COLOR_YELLOW = 16776960;
    public const COLOR_RED = 16711680;
    public const COLOR_BLUE = 255;
    public const COLOR_GREEN = 32768;
    public const COLOR_ORANGE = 16753920;
    public const COLOR_VIOLET = 15631086;

    private string $webhook;

    public function __construct(
        string $webhook,
        private Security $security,
        private HttpClientInterface $httpClient,
    ) {
        if ('Placeholder' === $webhook) {
            throw new Exception('Did you forget to set your webhook in your .env.local file?');
        }

        $this->webhook = $webhook;
    }

    public function send(string $title, string $message, int $color = self::COLOR_WHITE): void
    {
        $footer = $this->getUsername() . ' | ' . (new DateTime())->format('d.m.Y - H:i:s') . 'Uhr';

        $this->httpClient->request('POST', $this->webhook, [
            'json' => [
                'embeds' => [
                    [
                        'title' => $title,
                        'description' => $message,
                        'color' => $color,
                        'footer' => [
                            'text' => $footer,
                        ],
                    ],
                ],
            ],
        ]);
    }

    private function getUsername(): string
    {
        /** @var User|null $user */
        $user = $this->security->getUser();

        if (null === $user) {
            return 'Unknown';
        }

        return $user->getUsername();
    }
}
