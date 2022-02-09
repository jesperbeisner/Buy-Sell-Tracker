<?php

namespace App\Entity;

use App\Repository\SaleRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'string')]
    private int $amount;

    #[ORM\Column(type: 'float')]
    private float $blackMoney;

    #[ORM\Column(type: 'float')]
    private float $realMoney;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

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

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function getBlackMoney(): float
    {
        return $this->blackMoney;
    }

    public function setBlackMoney(float $blackMoney): void
    {
        $this->blackMoney = $blackMoney;
    }

    public function getRealMoney(): float
    {
        return $this->realMoney;
    }

    public function setRealMoney(float $realMoney): void
    {
        $this->realMoney = $realMoney;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }

    public function updateTime(): void
    {
        $dateTime = new DateTime();

        $this->created->setTime(
            $dateTime->format('H'),
            $dateTime->format('i'),
            $dateTime->format('s')
        );
    }
}
