<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 */
class Reservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=20, unique=true)
     */
    private $serial;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="reservations", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Traveler", mappedBy="reservation", cascade={"persist"})
     */
    private $travelers;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Options", inversedBy="reservations", cascade={"persist"})
     */
    private $options;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Stays", inversedBy="reservations", cascade={"persist"})
     */
    private $stays;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updateAt;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Payment", cascade={"persist", "remove"})
     */
    private $payment;


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

    public function getSerial(): ?string
    {
        return $this->serial;
    }

    public function setSerial(string $serial): self
    {
        $this->serial = $serial;

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

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    /**
     * @return Collection|Traveler[]
     */
    public function getTravelers(): Collection
    {
        return $this->travelers;
    }

    public function setTravelers( Collection $travelers )
    {
        $this->travlers = new ArrayCollection();
        foreach( $travelers as $traveler ){
            $this->addTraveler( $traveler );
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

    public function removeTraveler(Traveler $traveler): self
    {
        if ($this->travelers->contains($traveler)) {
            $this->travelers->removeElement($traveler);
            // set the owning side to null (unless already changed)
            if ($traveler->getReservation() === $this) {
                $traveler->setReservation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Options[]
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function setOptions( Collection $options )
    {
        foreach( $options as $option ){
            $this->addOption( $option );
        }
        return $this;
    }

    public function addOption(Options $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->setReservation($this);
        }

        return $this;
    }

    public function removeOption(Options $option): self
    {
        if ($this->options->contains($option)) {
            $this->options->removeElement($option);
            if ($option->getReservations() === $this) {
                $option->setReservation(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Stays[]
     */
    public function getStays(): Collection
    {
        return $this->stays;
    }

    public function setStays( Collection $stays )
    {
        foreach( $stays as $stay ){
            $this->addStay( $stay );
        }

        return $this;
    }

    public function addStay(Stays $stay): self
    {
        if (!$this->stays->contains($stay)) {
            $this->stays[] = $stay;
            $stay->setReservation($this);
        }

        return $this;
    }

    public function removeStay(Stays $stay): self
    {
        if ($this->stays->contains($stay)) {
            $this->stays->removeElement($stay);
            if ($stay->getReservations() === $this) {
                $stay->setReservation(null);
            }
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
