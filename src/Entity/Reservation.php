<?php

namespace App\Entity;

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

    #[ORM\Column(type: 'float')]
    private float $price;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reservations', cascade: ['persist'])]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    /** @var Collection<int, Traveler> */
    #[ORM\OneToMany(targetEntity: Traveler::class, mappedBy: 'reservation', cascade: ['persist', 'remove'])]
    private Collection $travelers;

    /** @var Collection<int, Options> */
    #[ORM\ManyToMany(targetEntity: Options::class, inversedBy: 'reservations', cascade: ['persist'])]
    private Collection $options;

    /** @var Collection<int, Stays> */
    #[ORM\ManyToMany(targetEntity: Stays::class, inversedBy: 'reservations', cascade: ['persist'])]
    private Collection $stays;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $updateAt = null;

    #[ORM\OneToOne(targetEntity: Payment::class, cascade: ['persist', 'remove'])]
    private ?Payment $payment = null;

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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
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
     * @return Collection<int, Options>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    /**
     * @param Collection<int, Options> $options
     */
    public function setOptions(Collection $options): static
    {
        $this->options = new ArrayCollection();
        foreach ($options as $option) {
            $this->addOption($option);
        }

        return $this;
    }

    public function addOption(Options $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->addReservation($this);
        }

        return $this;
    }

    public function removeOption(Options $option): self
    {
        if ($this->options->removeElement($option)) {
            $option->removeReservation($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Stays>
     */
    public function getStays(): Collection
    {
        return $this->stays;
    }

    /**
     * @param Collection<int, Stays> $stays
     */
    public function setStays(Collection $stays): static
    {
        $this->stays = new ArrayCollection();
        foreach ($stays as $stay) {
            $this->addStay($stay);
        }

        return $this;
    }

    public function addStay(Stays $stay): self
    {
        if (!$this->stays->contains($stay)) {
            $this->stays[] = $stay;
            $stay->addReservation($this);
        }

        return $this;
    }

    public function removeStay(Stays $stay): self
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
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getUpdateAt(): ?\DateTimeInterface
    {
        return $this->updateAt;
    }

    public function setUpdateAt(?\DateTimeInterface $updateAt): self
    {
        $this->updateAt = $updateAt;

        return $this;
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
}
