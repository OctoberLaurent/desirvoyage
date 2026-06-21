<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\FormalityRepository::class)]
class Formality
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 60)]
    private string $destination;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(min: 3, max: 1800, minMessage: 'Your description must be at least 10 characters long', maxMessage: 'Your description must not exceed 1800 characters')]
    private ?string $description = null;

    /** @var Collection<int, Travel> */
    #[ORM\ManyToMany(targetEntity: Travel::class, mappedBy: 'formality', cascade: ['persist'])]
    private Collection $travels;

    public function __construct()
    {
        $this->travels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDestination(): string
    {
        return $this->destination;
    }

    public function setDestination(string $destination): self
    {
        $this->destination = $destination;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return $this->destination;
    }
}
