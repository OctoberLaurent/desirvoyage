<?php

namespace App\Entity;

use App\ValueObject\Email;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\TravelerRepository::class)]
final class Traveler
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 80)]
    #[Assert\Length(min: 3, max: 80, minMessage: 'Your lastname must be at least {{ limit }} characters long.', maxMessage: 'Your lastname cannot be longer than {{ limit }} characters.')]
    private string $lastname;

    #[ORM\Column(type: 'string', length: 80)]
    #[Assert\Length(min: 3, max: 80, minMessage: 'Your firstname must be at least {{ limit }} characters long.', maxMessage: 'Your firstname cannot be longer than {{ limit }} characters.')]
    private string $firstname;

    #[ORM\Column(type: 'string', length: 50)]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    private string $email;

    #[ORM\ManyToOne(targetEntity: Reservation::class, inversedBy: 'travelers', cascade: ['persist'])]
    private ?Reservation $reservation = null;

    #[ORM\Column(type: 'datetime')]
    #[Assert\LessThan('-13 years')]
    private \DateTimeInterface $birthday;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): Email
    {
        return new Email($this->email);
    }

    public function setEmail(Email $email): self
    {
        $this->email = $email->value();

        return $this;
    }

    public function getBirthday(): \DateTimeInterface
    {
        return $this->birthday;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function setReservation(?Reservation $reservation): self
    {
        $this->reservation = $reservation;

        return $this;
    }
}
