<?php

namespace App\DataFixtures;

use App\Entity\Product;
use App\Entity\Seller;
use App\Entity\Shift;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (['Apfel', 'Tomate', 'Zitrone', 'Banane'] as $sellerName) {
            $seller = new Seller();
            $seller->setName($sellerName);
            $manager->persist($seller);
        }

        foreach (['Schlafmohn', 'Metall', 'Hai', 'Schildkröte', 'Baumwolle', 'Öl'] as $productName) {
            $product = new Product();
            $product->setName($productName);
            $manager->persist($product);
        }

        foreach (['13:00 Uhr', '17:00 Uhr', '20:00 Uhr', '23:00 Uhr'] as $shiftTime) {
            $shift = new Shift();
            $shift->setTime($shiftTime);
            $manager->persist($shift);
        }

        $manager->flush();
    }
}
