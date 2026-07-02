<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\TravelRepository::class)]
#[HasLifecycleCallbacks]
class Travel implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(min: 5, max: 30, minMessage: 'Your title must be at least 5 characters long', maxMessage: 'Your title must not exceed 30 characters')]
    #[Groups(['read'])]
    private string $name;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(min: 5, max: 50, minMessage: 'Your subtitle must be at least 5 characters long', maxMessage: 'Your subtitle must not exceed 50 characters')]
    #[Groups(['read'])]
    private string $subtitle;

    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\NotBlank(message: 'this field must not be empty')]
    #[Assert\Length(min: 10, max: 1800, minMessage: 'Your description must be at least 10 characters long', maxMessage: 'Your description must not exceed 1800 characters')]
    #[Groups(['read'])]
    private ?string $descriptions = null;

    /** @var Collection<int, Picture> */
    #[ORM\OneToMany(targetEntity: Picture::class, mappedBy: 'travel', cascade: ['persist', 'remove'])]
    private Collection $pictures;

    /** @var Collection<int, Stay> */
    #[ORM\OneToMany(targetEntity: Stay::class, mappedBy: 'travel', cascade: ['persist', 'remove'])]
    private Collection $stays;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'travel')]
    private ?Category $categories = null;

    /** @var Collection<int, Formality> */
    #[ORM\ManyToMany(targetEntity: Formality::class, inversedBy: 'travels', cascade: ['persist'])]
    private Collection $formality;

    /** @var Collection<int, Option> */
    #[ORM\ManyToMany(targetEntity: Option::class, inversedBy: 'travels', cascade: ['persist'])]
    #[ORM\JoinTable(name: 'travel_options')]
    #[ORM\JoinColumn(name: 'travel_id', referencedColumnName: 'id')]
    #[ORM\InverseJoinColumn(name: 'options_id', referencedColumnName: 'id')]
    private Collection $options;

    public function __construct()
    {
        $this->pictures = new ArrayCollection();
        $this->stays = new ArrayCollection();
        $this->options = new ArrayCollection();
        $this->formality = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function computeSlug(): void
    {
        $slugify = new Slugify();
        $this->slug = $slugify->slugify($this->name);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getSubtitle(): string
    {
        return $this->subtitle;
    }

    public function setSubtitle(string $subtitle): self
    {
        $this->subtitle = $subtitle;

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
     * @return Collection<int, Picture>
     */
    public function getPictures(): Collection
    {
        return $this->pictures;
    }

    public function addPicture(Picture $picture): self
    {
        if (!$this->pictures->contains($picture)) {
            $this->pictures[] = $picture;
            $picture->setTravel($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Stay>
     */
    public function getStays(): Collection
    {
        return $this->stays;
    }

    public function addStay(Stay $stay): self
    {
        if (!$this->stays->contains($stay)) {
            $this->stays[] = $stay;
            $stay->setTravel($this);
        }

        return $this;
    }

    public function getCategories(): ?Category
    {
        return $this->categories;
    }

    public function setCategories(?Category $categories): self
    {
        $this->categories = $categories;

        return $this;
    }

    /**
     * @return Collection<int, Formality>
     */
    public function getFormality(): Collection
    {
        return $this->formality;
    }

    public function getMinPrice(): float
    {
        $min = null;
        foreach ($this->stays as $stay) {
            $amount = $stay->getPrice()->amount();
            $min = null === $min ? $amount : min($min, $amount);
        }

        return $min ?? 0.0;
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
     * @return Collection<int, Option>
     */
    public function getOptions(): Collection
    {
        return $this->options;
    }

    public function addOptions(Option $options): self
    {
        if (!$this->options->contains($options)) {
            $this->options[] = $options;
            $options->addTravel($this);
        }

        return $this;
    }

    public function removeOptions(Option $options): self
    {
        if ($this->options->contains($options)) {
            $this->options->removeElement($options);
            $options->removeTravel($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
