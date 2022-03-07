<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\SaleRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SaleRepository::class)]
#[ORM\Index(fields: ['created'], name: 'sale_created_index')]
class Sale
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string')]
    private string $name;

    #[ORM\Column(type: 'integer')]
    private int $amount;

    #[ORM\Column(type: 'integer')]
    private int $blackMoney;

    #[ORM\Column(type: 'integer')]
    private int $realMoney;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $product;

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

    public function getBlackMoney(): int
    {
        return $this->blackMoney;
    }

    public function setBlackMoney(int $blackMoney): void
    {
        $this->blackMoney = $blackMoney;
    }

    public function getRealMoney(): int
    {
        return $this->realMoney;
    }

    public function setRealMoney(int $realMoney): void
    {
        $this->realMoney = $realMoney;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): void
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
}
