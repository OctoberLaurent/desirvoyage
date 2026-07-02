<?php

namespace App\Entity;

use App\ValueObject\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\OptionRepository::class)]
#[ORM\Table(name: 'options')]
class Option implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 60)]
    #[Assert\Length(min: 5, max: 60, minMessage: 'Your title must be at least 5 characters long', maxMessage: 'Your title must not exceed 60 characters')]
    private string $name;

    #[ORM\Column(type: 'text')]
    #[Assert\Length(min: 10, max: 400, minMessage: 'Your description must be at least 10 characters long', maxMessage: 'Your description must not exceed 400 characters')]
    private string $description;

    #[ORM\Column(type: 'string', length: 60)]
    #[Assert\Length(min: 3, max: 40, minMessage: 'This field must be have 3 characters long', maxMessage: 'This field must not exceed 40 characters long')]
    private string $type;

    /**
     * @var Collection<int, Travel>
     */
    #[ORM\ManyToMany(targetEntity: Travel::class, mappedBy: 'options', cascade: ['persist'])]
    private Collection $travels;

    #[ORM\Column(type: 'float')]
    private float $price;

    /**
     * @var Collection<int, Reservation>
     */
    #[ORM\ManyToMany(targetEntity: Reservation::class, mappedBy: 'options')]
    private Collection $reservations;

    public function __construct()
    {
        $this->travels = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getPrice(): Money
    {
        return new Money($this->price);
    }

    public function setPrice(Money|float $price): self
    {
        $this->price = $price instanceof Money ? $price->amount() : $price;

        return $this;
    }

    /**
     * @return Collection<int, Travel>
     */
    public function getTravels(): Collection
    {
        return $this->travels;
    }

    public function addTravel(Travel $travel): self
    {
        if (!$this->travels->contains($travel)) {
            $this->travels[] = $travel;
            $travel->addOptions($this);
        }

        return $this;
    }

    public function removeTravel(Travel $travel): self
    {
        if ($this->travels->removeElement($travel)) {
            $travel->removeOptions($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->addOption($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            $reservation->removeOption($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
