<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OptionsRepository")
 */
class Options
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\Length(min=10, max=100, minMessage="Your title must be at least 10 characters long", maxMessage="Your title must not exceed 100 characters")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\Length(min=10, max=100, minMessage="Your description must be at least 100 characters long", maxMessage="Your description must not exceed 1800 characters")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=60)
     * @Assert\Length(min=3, max=40, minMessage="This field must be have 3 characters long", maxMessage="This field must not exceed 40 characters long")
     */
    private $type;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Travel", mappedBy="options", cascade={"persist"})
     */
    private $travel;

    public function __construct()
    {
        $this->travels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    ###
       /**
     * @return Collection|Travel[]
     */
    public function getTravels(): Collection
    {
        return $this->travels;
    }

    public function addTravel(Travel $travel): self
    {
        if (!$this->travels->contains($travel)) {
            $this->travels[] = $travel;
            $travel->addOptions($this);
        }

        return $this;
    }

    public function removeTravel(Travel $travel): self
    {
        if ($this->travels->contains($travel)) {
            $this->travels->removeElement($travel);
            $travel->removeOptions($this);
        }

        return $this;
    }
    ###

    public function __toString()
    {
        return $this->name;
    }
}
