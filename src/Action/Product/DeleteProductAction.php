<?php

declare(strict_types=1);

namespace App\Action\Product;

use App\Action\AbstractAction;
use App\Entity\Product;
use App\Entity\Purchase;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class DeleteProductAction extends AbstractAction
{
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
        $request = $this->getRequest();

        if (null === $productId = $request->request->get('product-id')) {
            return new ActionResult(Result::FAILURE, 'Keine ID angegeben!');
        }

        if (null === $product = $this->entityManager->getRepository(Product::class)->find($productId)) {
            return new ActionResult(Result::FAILURE, 'Kein Produkt mit dieser ID gefunden!');
        }

        $this->entityManager->getRepository(Purchase::class)->setDeletedProductToNull($product);

        $this->entityManager->remove($product);
        $this->entityManager->flush();

        $this->discordNotifier->send(
            "Product deleted",
            "Product '{$product->getName()}' was successfully deleted",
            DiscordNotifier::COLOR_RED
        );

        return new ActionResult(Result::SUCCESS, 'Das Produkt wurde erfolgreich gelöscht!');
    }
}
