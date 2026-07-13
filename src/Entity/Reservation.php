<?php

namespace App\Entity;

use App\Enum\ReservationStatus;
use App\ValueObject\Money;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 20, unique: true)]
    private string $serial;

    #[ORM\Column(type: 'integer')]
    private int $price;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservations', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /** @var Collection<int, Traveler> */
    #[ORM\OneToMany(targetEntity: Traveler::class, mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private Collection $travelers;

    /** @var Collection<int, Option> */
    #[ORM\ManyToMany(targetEntity: Option::class, inversedBy: 'reservations', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'reservation_options')]
    #[ORM\JoinColumn(name: 'reservation_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'options_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $options;

    /** @var Collection<int, Stay> */
    #[ORM\ManyToMany(targetEntity: Stay::class, inversedBy: 'reservations', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'reservation_stays')]
    #[ORM\JoinColumn(name: 'reservation_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'stays_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Collection $stays;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdDate = null;

    #[ORM\Column(name: 'update_at', type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToOne(targetEntity: Payment::class, cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

    #[ORM\Column(type: 'string', length: 20, enumType: ReservationStatus::class, options: ['default' => 'pending'])]
    private ReservationStatus $status = ReservationStatus::Pending;

    public function __construct()
    {
        $this->travelers = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->stays = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSerial(): string
    {
        return $this->serial;
    }

    public function setSerial(string $serial): self
    {
        $this->serial = $serial;

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

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Traveler>
     */
    public function getTravelers(): Collection
    {
        return $this->travelers;
    }

    /**
     * @param Collection<int, Traveler> $travelers
     */
    public function setTravelers(Collection $travelers): static
    {
        $this->travelers = new ArrayCollection();
        foreach ($travelers as $traveler) {
            $this->addTraveler($traveler);
        }

        return $this;
    }

    public function addTraveler(Traveler $traveler): self
    {
        if (!$this->travelers->contains($traveler)) {
            $this->travelers[] = $traveler;
            $traveler->setReservation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    /**
     * @param Collection<int, Option> $options
     */
    public function setOptions(Collection $options): static
    {
        $this->options = new ArrayCollection();
        foreach ($options as $option) {
            $this->addOption($option);
        }

        return $this;
    }

    public function addOption(Option $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->addReservation($this);
        }

        return $this;
    }

    public function removeOption(Option $option): self
    {
        if ($this->options->removeElement($option)) {
            $option->removeReservation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Stay>
     */
    public function getStays(): Collection
    {
        return $this->stays;
    }

    /**
     * @param Collection<int, Stay> $stays
     */
    public function setStays(Collection $stays): static
    {
        $this->stays = new ArrayCollection();
        foreach ($stays as $stay) {
            $this->addStay($stay);
        }

        return $this;
    }

    public function addStay(Stay $stay): self
    {
        if (!$this->stays->contains($stay)) {
            $this->stays[] = $stay;
            $stay->addReservation($this);
        }

        return $this;
    }

    public function removeStay(Stay $stay): self
    {
        if ($this->stays->removeElement($stay)) {
            $stay->removeReservation($this);
        }

        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }

    public function setCreatedDate(?\DateTimeInterface $createdDate): self
    {
        $this->createdDate = null === $createdDate ? null : \DateTime::createFromInterface($createdDate);

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = null === $updatedAt ? null : \DateTime::createFromInterface($updatedAt);

        return $this;
    }

    /** @deprecated Utiliser getUpdatedAt(). */
    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->getUpdatedAt();
    }

    /** @deprecated Utiliser setUpdatedAt(). */
    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        return $this->setUpdatedAt($updateAt);
    }

    public function getPayment(): ?Payment
    {
        return $this->payment;
    }

    public function setPayment(?Payment $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Marks the reservation as paid by attaching its payment. The business
     * invariant (skill §3 Entity rules, §2 State) prevents a paid reservation
     * from being paid again. Prefer this method over externally calling setPayment().
     */
    public function markAsPaid(Payment $payment): void
    {
        if (ReservationStatus::Pending !== $this->status || null !== $this->payment) {
            throw new \DomainException('Seule une réservation en attente peut être payée.');
        }
        $this->payment = $payment;
        $this->status = ReservationStatus::Paid;
    }

    public function getStatus(): ReservationStatus
    {
        return $this->status;
    }

    public function expire(): void
    {
        if (ReservationStatus::Pending !== $this->status) {
            throw new \DomainException('Seule une réservation en attente peut expirer.');
        }

        $this->status = ReservationStatus::Expired;
    }

    public function belongsTo(User $user): bool
    {
        if ($this->user === $user) {
            return true;
        }

        return null !== $this->user->getId()
            && null !== $user->getId()
            && $this->user->getId() === $user->getId();
    }
}
