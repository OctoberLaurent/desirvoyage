<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StaysRepository")
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
     * @Assert\GreaterThan("today")
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
}
