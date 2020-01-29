<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(
     *      min=6,
     *      max=30,
     *      minMessage="Your pasword must be at least {{limit}} characters long.",
     *      maxMessage="Your password cannot be longuer than {{limit}} characters."
     * )
     * @Assert\NotBlank()
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\Length(
     *     min=3,
     *     max=80,
     *     minMessage="Your firstname must be at least {{ limit }} characters long.",
     *     maxMessage="Your firstname cannot be longer than {{ limit }} characters."
     * )
     * @Assert\NotBlank()
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\Length(
     *     min=3,
     *     max=80,
     *     minMessage="Your lastname must be at least {{ limit }} characters long.",
     *     maxMessage="Your lastname cannot be longer than {{ limit }} characters."
     * )
     * @Assert\NotBlank()
     */
    private $lastname;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enabled = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $token;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $tokenExpire;

    /**
     * @ORM\Column(type="string", length=90)
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=80, nullable=true)
     */
    private $additionalAddress;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $city;

    /**
     * @ORM\Column(type="string", length=80)
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(min = 9, max = 10, minMessage = "min_length", maxMessage = "max_length")
     * @Assert\Regex(pattern = "/^[0-9]*$/", message = "number_only")
     */
    private $phone;

    /**
     * @ORM\Column(type="string", length=10)
     * @Assert\Length(min = 5, max = 5, minMessage = "min_length", maxMessage = "max_length")
     */
    private $postalCode;

    /**
     * @ORM\Column(type="datetime")
     */
    private $birthday;

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
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
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
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getAdditionalAddress(): ?string
    {
        return $this->additionalAddress;
    }

    public function setAdditionalAddress(string $additionalAddress): self
    {
        $this->additionalAddress = $additionalAddress;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(string $postalCode): self
    {
        $this->postalCode = $postalCode;

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
