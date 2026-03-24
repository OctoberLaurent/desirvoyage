<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\UserRepository::class)]
#[UniqueEntity('email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email(message: "The email '{{ value }}' is not a valid email.")]
    private $email;

    #[ORM\Column(type: 'json')]
    private $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column(type: 'string', length: 255)]
    private $password;

    #[ORM\Column(type: 'string', length: 80)]
    #[Assert\Length(min: 3, max: 80, minMessage: 'Your firstname must be at least {{ limit }} characters long.', maxMessage: 'Your firstname cannot be longer than {{ limit }} characters.')]
    #[Assert\NotBlank]
    private $firstname;

    #[ORM\Column(type: 'string', length: 80)]
    #[Assert\Length(min: 3, max: 80, minMessage: 'Your lastname must be at least {{ limit }} characters long.', maxMessage: 'Your lastname cannot be longer than {{ limit }} characters.')]
    #[Assert\NotBlank]
    private $lastname;

    #[ORM\Column(type: 'boolean')]
    private $enabled = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $token;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $tokenExpire;

    #[ORM\Column(type: 'string', length: 90)]
    private $address;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private $additionalAddress;

    #[ORM\Column(type: 'string', length: 80)]
    private $city;

    #[ORM\Column(type: 'string', length: 80)]
    private $country;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Length(min: 9, max: 10, minMessage: 'min_length', maxMessage: 'max_length')]
    private $phone;

    #[ORM\Column(type: 'string', length: 20)]
    #[Assert\Length(min: 5, max: 5, minMessage: 'min_length', maxMessage: 'max_length')]
    private $postalCode;

    #[ORM\Column(type: 'datetime')]
    #[Assert\LessThan('-13 years')]
    private $birthday;

    #[ORM\OneToMany(targetEntity: Reservation::class, mappedBy: 'user')]
    private $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    #[\Override]
    public function getUserIdentifier(): string
    {
        return $this->email ?? 'anonymous';
    }

    /**
     * @see UserInterface
     */
    #[\Override]
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    #[\Override]
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    #[\Override]
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFullName(): string
    {
        return trim($this->firstname.' '.$this->lastname);
    }

    public function getEnabled(): ?bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(?string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getTokenExpire(): ?\DateTimeInterface
    {
        return $this->tokenExpire;
    }

    public function setTokenExpire(?\DateTimeInterface $tokenExpire): self
    {
        $this->tokenExpire = $tokenExpire;

        return $this;
    }

    public function getAdditionalAddress(): ?string
    {
        return $this->additionalAddress;
    }

    public function setAdditionalAddress(?string $additionalAddress): self
    {
        $this->additionalAddress = $additionalAddress;

        return $this;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function setBirthday(\DateTimeInterface $birthday): self
    {
        $this->birthday = $birthday;

        return $this;
    }

    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function __toString()
    {
        return $this->firstname;
    }
}
