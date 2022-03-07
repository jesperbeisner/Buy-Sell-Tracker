<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PurchaseRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchaseRepository::class)]
#[ORM\Index(fields: ['created'], name: 'purchase_created_index')]
class Purchase
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\ManyToOne(targetEntity: Shift::class)]
    private ?Shift $shift;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $product;

    #[ORM\ManyToOne(targetEntity: Fraction::class)]
    private ?Fraction $fraction;

    #[ORM\Column(type: 'datetime')]
    private DateTime $created;

    public function __construct()
    {
        $this->created = new DateTime();
    }

    public function updateTime(): void
    {
        $dateTime = new DateTime();

        $this->created->setTime(
            (int) $dateTime->format('H'),
            (int) $dateTime->format('i'),
            (int) $dateTime->format('s')
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getShift(): ?Shift
    {
        return $this->shift;
    }

    public function setShift(?Shift $shift): void
    {
        $this->shift = $shift;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
    {
        $this->product = $product;
    }

    public function getFraction(): ?Fraction
    {
        return $this->fraction;
    }

    public function setFraction(?Fraction $fraction): void
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
