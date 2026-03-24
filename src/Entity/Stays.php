<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ApiResource(
 *     normalizationContext={"groups"={"read"}},
 *     itemOperations={"get"},
 *     collectionOperations={"get"},
 * )
 */
#[ORM\Entity(repositoryClass: \App\Repository\StaysRepository::class)]
#[HasLifecycleCallbacks]
final class Stays
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    #[Groups(['read'])]
    private $starDate;

    #[ORM\Column(type: 'datetime')]
    #[Assert\GreaterThan(propertyPath: 'starDate', message: "La date de départ doit être plus éloignée que la date d'arrivée !")]
    #[Groups(['read'])]
    private $endDate;

    #[ORM\Column(type: 'string', length: 60)]
    #[Assert\Length(min: 3, max: 40, minMessage: 'This field must be have 3 characters long', maxMessage: 'This field must not exceed 60 characters long')]
    #[Groups(['read'])]
    private $depature;

    #[ORM\Column(type: 'string', length: 60)]
    #[Assert\Length(min: 3, max: 40, minMessage: 'This field must be have 3 characters long', maxMessage: 'This field must not exceed 60 characters long')]
    #[Groups(['read'])]
    private $arrival;

    #[ORM\Column(type: 'float')]
    #[Assert\Type(type: 'float')]
    #[Groups(['read'])]
    private $price;

    #[ORM\ManyToOne(targetEntity: Travel::class, inversedBy: 'stays', cascade: ['persist'])]
    #[Groups(['read'])]
    private $travel;

    #[ORM\ManyToMany(targetEntity: Reservation::class, mappedBy: 'stays')]
    private $reservations;

    #[ORM\Column(type: 'integer')]
    #[Groups(['read'])]
    private $stock;

    #[ORM\Column(type: 'string', length: 255, nullable: true, unique: true)]
    private $serial;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Assert\GreaterThan('today')]
    private $createdDate;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStarDate(): ?\DateTimeInterface
    {
        return $this->starDate;
    }

    public function setStarDate(\DateTimeInterface $starDate): self
    {
        $this->starDate = $starDate;

        return $this;
    }

    public function getEndDate(): ?\DateTimeInterface
    {
        return $this->endDate;
    }

    public function setEndDate(\DateTimeInterface $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }

    public function getDepature(): ?string
    {
        return $this->depature;
    }

    public function setDepature(string $depature): self
    {
        $this->depature = $depature;

        return $this;
    }

    public function getArrival(): ?string
    {
        return $this->arrival;
    }

    public function setArrival(string $arrival): self
    {
        $this->arrival = $arrival;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
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

    public function __toString()
    {
        return $this->depature;
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

    public function getStock(): ?int
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
    public function setSerial(): self
    {
        $this->serial = $this->serialEasy();

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedDate(): self
    {
        $this->createdDate = new \DateTime();

        return $this;
    }

    public function serialEasy(): string
    {
        return uniqid();
    }
}
