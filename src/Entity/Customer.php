<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CustomerRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $condition;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private Product $product;

    #[ORM\ManyToOne(targetEntity: Fraction::class)]
    private Fraction $fraction;

    #[ORM\Column(type: 'datetime')]
    private DateTime $created;

    public function __construct()
    {
        $this->created = new DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCondition(): int
    {
        return $this->condition;
    }

    public function setCondition(int $condition): void
    {
        $this->condition = $condition;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getFraction(): Fraction
    {
        return $this->fraction;
    }

    public function setFraction(Fraction $fraction): void
    {
        $this->fraction = $fraction;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }
}
