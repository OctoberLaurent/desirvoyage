<?php

namespace App\Entity;

use App\ValueObject\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\StayRepository::class)]
#[ORM\Table(name: 'stays')]
#[HasLifecycleCallbacks]
final class Stay implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'star_date', type: 'datetime')]
    #[Groups(['read'])]
    private \DateTimeInterface $startDate;

    #[ORM\Column(type: 'datetime')]
    #[Assert\GreaterThan(propertyPath: 'startDate', message: 'La date de retour doit être postérieure à la date de départ.')]
    #[Groups(['read'])]
    private \DateTimeInterface $endDate;

    #[ORM\Column(name: 'depature', type: 'string', length: 60)]
    #[Assert\Length(min: 3, max: 40, minMessage: 'This field must be have 3 characters long', maxMessage: 'This field must not exceed 60 characters long')]
    #[Groups(['read'])]
    private string $departure;

    #[ORM\Column(type: 'string', length: 60)]
    #[Assert\Length(min: 3, max: 40, minMessage: 'This field must be have 3 characters long', maxMessage: 'This field must not exceed 60 characters long')]
    #[Groups(['read'])]
    private string $arrival;

    #[ORM\Column(type: 'integer')]
    #[Groups(['read'])]
    private int $price;

    #[ORM\ManyToOne(targetEntity: Travel::class, inversedBy: 'stays', cascade: ['persist'])]
    #[Groups(['read'])]
    private ?Travel $travel = null;

    /** @var Collection<int, Reservation> */
    #[ORM\ManyToMany(targetEntity: Reservation::class, mappedBy: 'stays')]
    private Collection $reservations;

    #[ORM\Column(type: 'integer')]
    #[Groups(['read'])]
    private int $stock;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    private ?string $serial = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\GreaterThan('today')]
    private ?\DateTimeInterface $createdDate = null;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStartDate(): \DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    /** @deprecated Use getStartDate(). */
    public function getStarDate(): \DateTimeInterface
    {
        return $this->getStartDate();
    }

    /** @deprecated Use setStartDate(). */
    public function setStarDate(\DateTimeInterface $starDate): self
    {
        return $this->setStartDate($starDate);
    }

    public function getEndDate(): \DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDeparture(): string
    {
        return $this->departure;
    }

    public function setDeparture(string $departure): self
    {
        $this->departure = $departure;

        return $this;
    }

    /** @deprecated Use getDeparture(). */
    public function getDepature(): string
    {
        return $this->getDeparture();
    }

    /** @deprecated Use setDeparture(). */
    public function setDepature(string $depature): self
    {
        return $this->setDeparture($depature);
    }

    public function getArrival(): string
    {
        return $this->arrival;
    }

    public function setArrival(string $arrival): self
    {
        $this->arrival = $arrival;

        return $this;
    }

    public function getPrice(): Money
    {
        return Money::fromCents($this->price);
    }

    public function setPrice(Money|int|float|string $price): self
    {
        $this->price = $price instanceof Money ? $price->cents() : Money::fromEuros($price)->cents();

        return $this;
    }

    public function getPriceAmount(): float
    {
        return $this->getPrice()->amount();
    }

    public function setPriceAmount(int|float|string $price): self
    {
        return $this->setPrice($price);
    }

    public function getTravel(): ?Travel
    {
        return $this->travel;
    }

    public function setTravel(?Travel $travel): self
    {
        $this->travel = $travel;

        return $this;
    }

    #[\Override]
    public function __toString(): string
    {
        return $this->departure;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->addStay($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            $reservation->removeStay($this);
        }

        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    #[ORM\PrePersist]
    public function initializeSerial(): void
    {
        $this->serial ??= $this->generateSerial();
    }

    #[ORM\PrePersist]
    public function setCreatedDate(): self
    {
        $this->createdDate = new \DateTime();

        return $this;
    }

    public function generateSerial(): string
    {
        $raw = strtoupper(substr(bin2hex(random_bytes(6)), 0, 9));

        return implode('-', str_split($raw, 3));
    }
}
