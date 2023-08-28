<?php

namespace App\Entity;

use App\Repository\StockPortfolioRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: StockPortfolioRepository::class)]
class StockPortfolio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'stockPortfolios')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Portfolio $Portfolio = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Stock $Stock = null;

    #[ORM\Column]
    private ?int $amount = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPortfolio(): ?Portfolio
    {
        return $this->Portfolio;
    }

    public function setPortfolio(?Portfolio $Portfolio): static
    {
        $this->Portfolio = $Portfolio;

        return $this;
    }

    public function getStock(): ?Stock
    {
        return $this->Stock;
    }

    public function setStock(?Stock $Stock): static
    {
        $this->Stock = $Stock;

        return $this;
    }

    public function getAmount(): ?int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): static
    {
        $this->amount = $amount;

        return $this;
    }
}
