<?php

declare(strict_types=1);

namespace App\Action\Sale;

use App\Action\AbstractAction;
use App\Entity\Sale;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class DeleteSaleAction extends AbstractAction
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
            throw new Exception('Id in DeleteSaleAction can not be null. Did you forget to set the id before calling execute?');
        }

        if (null === $sale = $this->entityManager->getRepository(Sale::class)->find($this->id)) {
            return new ActionResult(Result::FAILURE, 'No sale found with this id', ['code' => 404]);
        }

        $product = $sale->getProduct();
        $productName = $product === null ? '-' : $product->getName();

        $this->entityManager->remove($sale);
        $this->entityManager->flush();

        $discordNotifierMessage = "The sale was successfully deleted." . PHP_EOL . PHP_EOL;
        $discordNotifierMessage .= "Amount: {$sale->getAmount()}; Black Money: {$sale->getBlackMoney()}; ";
        $discordNotifierMessage .= "Real Money: {$sale->getRealMoney()}; Product: $productName; ";
        $discordNotifierMessage .= "User: {$sale->getName()}; Created: {$sale->getCreated()->format('d.m.Y - H:i:s')}";

        $this->discordNotifier->send(
            "Sale deleted",
            $discordNotifierMessage,
            DiscordNotifier::COLOR_RED
        );

        return new ActionResult(Result::SUCCESS, 'The sale was successfully deleted', ['code' => 200]);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
