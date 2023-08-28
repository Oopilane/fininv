<?php

namespace App\Entity;

use App\Repository\PortfolioRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PortfolioRepository::class)]
class Portfolio
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'portfolio', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\OneToMany(mappedBy: 'Portfolio', targetEntity: StockPortfolio::class, orphanRemoval: true)]
    private Collection $stockPortfolios;

    public function __construct()
    {
        $this->stockPortfolios = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(User $User): static
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @return Collection<int, StockPortfolio>
     */
    public function getStockPortfolios(): Collection
    {
        return $this->stockPortfolios;
    }

    public function addStockPortfolio(StockPortfolio $stockPortfolio): static
    {
        if (!$this->stockPortfolios->contains($stockPortfolio)) {
            $this->stockPortfolios->add($stockPortfolio);
            $stockPortfolio->setPortfolio($this);
        }

        return $this;
    }

    public function removeStockPortfolio(StockPortfolio $stockPortfolio): static
    {
        if ($this->stockPortfolios->removeElement($stockPortfolio)) {
            // set the owning side to null (unless already changed)
            if ($stockPortfolio->getPortfolio() === $this) {
                $stockPortfolio->setPortfolio(null);
            }
        }

        return $this;
    }
}
