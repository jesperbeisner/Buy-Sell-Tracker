<?php

namespace App\Entity;

use App\Repository\WeekRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WeekRepository::class)]
#[ORM\Index(fields: ['week', 'year'], name: 'week_year_index')]
class Week
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'integer')]
    private int $week;

    #[ORM\Column(type: 'integer')]
    private int $year = 2022;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private Product $product;

    #[ORM\Column(type: 'float')]
    private float $blackMoney = 0;

    #[ORM\Column(type: 'float')]
    private float $realMoney = 0;

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

    public function getWeek(): int
    {
        return $this->week;
    }

    public function setWeek(int $week): void
    {
        $this->week = $week;
    }

    public function getYear(): int
    {
        return $this->year;
    }

    public function setYear(int $year): void
    {
        $this->year = $year;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
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

    public function getCreated(): DateTime
    {
        return $this->created;
    }

    public function setCreated(DateTime $created): void
    {
        $this->created = $created;
    }
}
