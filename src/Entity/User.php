<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Ignore;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Ignore]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Ignore]
    private ?string $email = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $username = null;

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Ignore]
    private ?string $password = null;
    
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Transaction::class)]
    private Collection $transactions;

    #[ORM\Column]
    #[Ignore]
    private array $roles = [];

    #[ORM\Column]
    private ?float $balance = null;

    #[ORM\OneToOne(mappedBy: 'User', cascade: ['persist', 'remove'])]
    private ?Portfolio $portfolio = null;

    #[ORM\Column]
    private ?float $profit = null;



    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }
    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }
    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): static
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
            $transaction->setUser($this);
        }

        return $this;
    }

    public function removeTransaction(Transaction $transaction): static
    {
        if ($this->transactions->removeElement($transaction)) {
            // set the owning side to null (unless already changed)
            if ($transaction->getUser() === $this) {
                $transaction->setUser(null);
            }
        }

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getBalance(): ?float
    {
        return $this->balance;
    }

    public function setBalance(float $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getPortfolio(): ?Portfolio
    {
        return $this->portfolio;
    }

    public function setPortfolio(Portfolio $portfolio): static
    {
        // set the owning side of the relation if necessary
        if ($portfolio->getUser() !== $this) {
            $portfolio->setUser($this);
        }

        $this->portfolio = $portfolio;

        return $this;
    }

    public function getProfit(): ?float
    {
        return $this->profit;
    }

    public function setProfit(float $profit): static
    {
        $this->profit = $profit;

        return $this;
    }
}
