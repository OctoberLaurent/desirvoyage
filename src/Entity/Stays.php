<?php

namespace App\Entity;

use DateTime;
use Exception;
use Doctrine\ORM\Mapping as ORM;
use App\Service\MakeSerialService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\StaysRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Stays
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $starDate;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\GreaterThan(propertyPath="starDate", message="La date de départ doit être plus éloignée que la date d'arrivée !")
     */
    private $endDate;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\Length(min=3, max=40, minMessage="This field must be have 3 characters long", maxMessage="This field must not exceed 60 characters long")
     */
    private $depature;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\Length(min=3, max=40, minMessage="This field must be have 3 characters long", maxMessage="This field must not exceed 60 characters long")
     */
    private $arrival;

    /**
     * @ORM\Column(type="float")
     * @Assert\Type(type="float")
     */
    private $price;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Travel", inversedBy="stays", cascade={"persist"})
     */
    private $travel;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Reservation", mappedBy="stays")
     */
    private $reservations;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, unique=true)
     */
    private $serial;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\GreaterThan("today")
     */
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

    /**
     * @return Collection|Reservation[]
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations[] = $reservation;
            $reservation->addStay($this);
        }

        return $this;
    }

    public function setReservation(?Reservation $reservation): self
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->contains($reservation)) {
            $this->reservations->removeElement($reservation);
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

    /**
     * @ORM\PrePersist
     */
    public function setSerial(): self
    {
        
            $this->serial = $this->serialEasy();
       
      
        
        return $this;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate;
    }
    
    /**
     * @ORM\PrePersist
     */
    public function setCreatedDate(): self
    {
        $this->createdDate = new \DateTime();

        return $this;
    }
    public function serialEasy(){
        for($i=0;$i<20;$i++){
            $serial = uniqid();
        }
        return $serial;
    }
}
