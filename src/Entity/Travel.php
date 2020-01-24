<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TravelRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Travel
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $subtitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $descriptions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pictures", mappedBy="travel",cascade={"persist"})
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Stays", mappedBy="travel",cascade={"persist"})
     */
    private $stays;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categories", inversedBy="travel")
     */
    private $categories;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Options", mappedBy="travel")
     */
    private $options;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Formality", inversedBy="travels")
     */
    private $formality;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->stays = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->formality = new ArrayCollection();
    }

     /**
     * Permet d'initialiser le slug !
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     * @return void
     */
    public function initializeSlug() {
        if(empty($this->slug)) {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
        }
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

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getDescriptions(): ?string
    {
        return $this->descriptions;
    }

    public function setDescriptions(?string $descriptions): self
    {
        $this->descriptions = $descriptions;

        return $this;
    }

    /**
     * @return Collection|Pictures[]
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Pictures $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setTravel($this);
        }

        return $this;
    }

    public function removePicture(Pictures $picture): self
    {
        if ($this->pictures->contains($picture)) {
            $this->pictures->removeElement($picture);
            // set the owning side to null (unless already changed)
            if ($picture->getTravel() === $this) {
                $picture->setTravel(null);
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

    public function addStay(Stays $stay): self
    {
        if (!$this->stays->contains($stay)) {
            $this->stays[] = $stay;
            $stay->setTravel($this);
        }

        return $this;
    }

    public function removeStay(Stays $stay): self
    {
        if ($this->stays->contains($stay)) {
            $this->stays->removeElement($stay);
            // set the owning side to null (unless already changed)
            if ($stay->getTravel() === $this) {
                $stay->setTravel(null);
            }
        }

        return $this;
    }

    public function getCategories(): ?Categories
    {
        return $this->categories;
    }

    public function setCategories(?Categories $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection|Options[]
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOption(Options $option): self
    {
        if (!$this->options->contains($option)) {
            $this->options[] = $option;
            $option->setTravel($this);
        }

        return $this;
    }

    public function removeOption(Options $option): self
    {
        if ($this->options->contains($option)) {
            $this->options->removeElement($option);
            // set the owning side to null (unless already changed)
            if ($option->getTravel() === $this) {
                $option->setTravel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Formality[]
     */
    public function getFormality(): Collection
    {
        return $this->formality;
    }

    public function addFormality(Formality $formality): self
    {
        if (!$this->formality->contains($formality)) {
            $this->formality[] = $formality;
        }

        return $this;
    }

    public function removeFormality(Formality $formality): self
    {
        if ($this->formality->contains($formality)) {
            $this->formality->removeElement($formality);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
