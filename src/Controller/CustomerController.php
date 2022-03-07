<?php

declare(strict_types=1);

namespace App\Controller;

use _PHPStan_3e014c27f\Nette\Utils\Json;
use App\Action\Customer\DeleteCustomerAction;
use App\Entity\Customer;
use App\Entity\Fraction;
use App\Entity\Product;
use App\Form\CustomerType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CustomerController extends AbstractController
{
    #[Route('/customers', name: 'customers')]
    public function customers(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        $customerForm = $this->createForm(CustomerType::class);
        $customerForm->handleRequest($request);

        if ($customerForm->isSubmitted() && $customerForm->isValid()) {
            /** @var Customer $customer */
            $customer = $customerForm->getViewData();

            if (null !== $entityManager->getRepository(Customer::class)->findByName($customer->getName())) {
                $this->addFlash('error', 'Ein Kunde mit diesem Namen existiert bereits');
                return $this->redirectToRoute('customers');
            }

            $entityManager->persist($customer);
            $entityManager->flush();

            $this->addFlash('success', 'Der Kunde wurde erfolgreich hinzugefügt');
            return $this->redirectToRoute('customers');
        }

        return $this->render('customer/index.html.twig', [
            'customerForm' => $customerForm->createView(),
            'customers' => $entityManager->getRepository(Customer::class)->findBy([], ['name' => 'ASC']),
            'products' => $entityManager->getRepository(Product::class)->findAll(),
            'fractions' => $entityManager->getRepository(Fraction::class)->findAll(),
        ]);
    }

    #[Route('/customers/{id<\d+>}', name: 'delete-customer', methods: ['DELETE'])]
    public function deleteCustomer(int $id, DeleteCustomerAction $deleteCustomerAction): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_SUPER_USER');

        $deleteCustomerAction->setId($id);
        $deleteCustomerResult = $deleteCustomerAction->execute();

        /** @var array<string, int> $data */
        $data = $deleteCustomerResult->getData();

        return new JsonResponse(['message' => $deleteCustomerResult->getMessage()], $data['code']);
    }
}
