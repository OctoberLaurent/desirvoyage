<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;


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
     * @Assert\Length(min=10, max=30, minMessage="Your title must be at least 10 characters long", maxMessage="Your title must not exceed 30 characters")
     * @Groups({"read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min=10, max=50, minMessage="Your subtitle must be at least 10 characters long", maxMessage="Your subtitle must not exceed 50 characters")
     * @Groups({"read"})
     */
    private $subtitle;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Assert\NotBlank(message="this field must not be empty")
     * @Assert\Length(min=10, max=1800, minMessage="Your description must be at least 10 characters long", maxMessage="Your description must not exceed 1800 characters")
     * @Groups({"read"})
     */
    private $descriptions;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Pictures", mappedBy="travel",cascade={"persist","remove"})
     */
    private $pictures;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Stays", mappedBy="travel",cascade={"persist", "remove"})
     */
    private $stays;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Categories", inversedBy="travel")
     */
    private $categories;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Formality", inversedBy="travels", cascade={"persist"})
     */
    private $formality;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Options", inversedBy="travels", cascade={"persist"})
     */
    private $options;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->stays = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->formality = new ArrayCollection();
    }

     /**
     * return a slug !
     *
     * @ORM\PrePersist
     * @ORM\PreUpdate
     * 
     * @return void
     */
    public function initializeSlug() {
            $slugify = new Slugify();
            $this->slug = $slugify->slugify($this->name);
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

    /**
     * @return Collection|Options[]
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOptions(Options $options): self
    {
        if (!$this->options->contains($options)) {
            $this->options[] = $options;
        }

        return $this;
    }

    public function removeOptions(Options $options): self
    {
        if ($this->options->contains($options)) {
            $this->options->removeElement($options);
        }

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
    
    /**
     * Get min price for a travel
     *
     * @return float|null
     */
    public function getMinPrice(): ?float
    {
        $minprice = null;

        foreach ($this->stays as $stay){

            ($minprice == null)? $minprice = $stay->getPrice() : null;

            ($stay->getPrice() < $minprice)? $minprice = $stay->getPrice() : null ;
             
        };

        return $minprice;
    }
}
