<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TravelerRepository")
 */
class Traveler
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\Length(
     *     min=3,
     *     max=80,
     *     minMessage="Your lastname must be at least {{ limit }} characters long.",
     *     maxMessage="Your lastname cannot be longer than {{ limit }} characters."
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\Length(
     *     min=3,
     *     max=80,
     *     minMessage="Your firstname must be at least {{ limit }} characters long.",
     *     maxMessage="Your firstname cannot be longer than {{ limit }} characters."
     * )
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     */
    private $email;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Reservation", inversedBy="travelers", cascade={"persist"})
     */
    private $reservation;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\LessThan("-13 years")
     *     
     */
    private $birthday;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): self
    {
        $this->reservation = $reservation;

        return $this;
    }

    public function getBirthday(): ?\DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

}
