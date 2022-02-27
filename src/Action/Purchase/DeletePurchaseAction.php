<?php

declare(strict_types=1);

namespace App\Action\Purchase;

use App\Action\AbstractAction;
use App\Entity\Entry;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

final class DeletePurchaseAction extends AbstractAction
{
    private ?int $id = null;

    public function __construct(
        Security $security,
        RequestStack $requestStack,
        DiscordNotifier $discordNotifier,
        private EntityManagerInterface $entityManager,
    ) {
        parent::__construct($security, $requestStack, $discordNotifier);
    }

    public function execute(): ActionResult
    {
        if (null === $this->id) {
            throw new Exception('Id in DeletePurchaseAction can not be null. Did you forget to set the id before calling execute?');
        }

        if (null === $purchase = $this->entityManager->getRepository(Entry::class)->find($this->id)) {
            return new ActionResult(Result::FAILURE, 'No purchase found with this id', ['code' => 404]);
        }

        $shift = $purchase->getShift();
        $product = $purchase->getProduct();
        $seller = $purchase->getSeller();
        $username = $purchase->getName() ?? 'Unknown';

        $this->entityManager->remove($purchase);
        $this->entityManager->flush();

        $discordNotifierMessage = "The purchase was successfully deleted." . PHP_EOL . PHP_EOL;
        $discordNotifierMessage .= "Amount: {$purchase->getAmount()}; Price: {$purchase->getPrice()}; ";
        $discordNotifierMessage .= "Shift: {$shift->getTime()}; Product: {$product->getName()}; Seller: {$seller->getName()}; ";
        $discordNotifierMessage .= "User: $username; Created: {$purchase->getCreated()->format('d.m.Y - H:i:s')}";

        $this->discordNotifier->send(
            "Purchase deleted",
            $discordNotifierMessage,
            DiscordNotifier::COLOR_RED
        );

        return new ActionResult(Result::SUCCESS, 'The purchase was successfully deleted', ['code' => 200]);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
