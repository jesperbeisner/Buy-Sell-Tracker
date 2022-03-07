<?php

declare(strict_types=1);

namespace App\Action\Product;

use App\Action\AbstractAction;
use App\Entity\Product;
use App\Notifier\DiscordNotifier;
use App\Result\ActionResult;
use App\Result\Result;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Security;

class CreateProductAction extends AbstractAction
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

        if (null === $productName = $request->request->get('product')) {
            return new ActionResult(Result::FAILURE, 'Kein Produkt angegeben!');
        }

        $productName = (string) $productName;

        if (strlen($productName) < 3) {
            return new ActionResult(Result::FAILURE, 'Der Name für eine neues Produkt muss mindestens 3 Zeichen lang sein!');
        }

        if (null !== $this->entityManager->getRepository(Product::class)->findByName($productName)) {
            return new ActionResult(Result::FAILURE, 'Ein Produkt mit diesem Namen existiert bereits!');
        }

        $product = new Product();
        $product->setName($productName);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        $this->discordNotifier->send(
            "Product added",
            "Product '$productName' was added successfully",
            DiscordNotifier::COLOR_GREEN
        );

        return new ActionResult(Result::SUCCESS, 'Das Produkt wurde erfolgreich hinzugefügt!');
    }
}
