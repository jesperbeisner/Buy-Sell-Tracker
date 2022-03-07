<?php

declare(strict_types=1);

namespace App\Action\Customer;

use App\Action\AbstractAction;
use App\Entity\Customer;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class DeleteCustomerAction extends AbstractAction
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
            throw new Exception('Id in DeleteCustomerAction can not be null. Did you forget to set the id before calling execute?');
        }

        if (null === $customer = $this->entityManager->getRepository(Customer::class)->find($this->id)) {
            return new ActionResult(Result::FAILURE, 'No customer found with this id', ['code' => 404]);
        }

        $product = $customer->getProduct();
        $productName = $product === null ? '-' : $product->getName();

        $fraction = $customer->getFraction();
        $fractionName = $fraction === null ? '-' : $fraction->getName();

        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        $discordNotifierMessage = "The customer was successfully deleted." . PHP_EOL . PHP_EOL;
        $discordNotifierMessage .= "Name: {$customer->getName()}; Condition: {$customer->getCondition()}; ";
        $discordNotifierMessage .= "Product: $productName; Fraction: $fractionName; ";
        $discordNotifierMessage .= "Created: {$customer->getCreated()->format('d.m.Y - H:i:s')}";

        $this->discordNotifier->send(
            "Customer deleted",
            $discordNotifierMessage,
            DiscordNotifier::COLOR_RED
        );

        return new ActionResult(Result::SUCCESS, 'The customer was successfully deleted', ['code' => 200]);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}
