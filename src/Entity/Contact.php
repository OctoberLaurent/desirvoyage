<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ContactRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Contact
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
     *      min=3,
     *      max=80,
     *      minMessage="Your lastname must be at least {{ limit }} characters long.",
     *      maxMessage="Your lastname cannot be longer than {{ limit }} characters."
     * )
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=80)
     * @Assert\Length(
     *      min=3,
     *      max=80,
     *      minMessage="Your firstname must be at least {{ limit }} characters long.",
     *      maxMessage="Your firstname cannot be longer than {{ limit }} characters."
     * ) 
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     */
    private $email;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10, max=1800, minMessage="Your description must be at least 10 characters long", maxMessage="Your description must not exceed 1800 characters")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     * 
     */
    private $SendDate;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getSendDate(): ?\DateTimeInterface
    {
        return $this->SendDate;
    }

    public function setSendDate(\DateTimeInterface $SendDate): self
    {
        $this->SendDate = $SendDate;

        return $this;
    }
}
