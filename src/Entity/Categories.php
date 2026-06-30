<?php

namespace App\Entity;

use Cocur\Slugify\Slugify;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\CategoriesRepository::class)]
#[HasLifecycleCallbacks]
final class Categories implements \Stringable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\Length(min: 4, max: 30, minMessage: 'Your title must be at least 4 characters long', maxMessage: 'Your title must not exceed 30 characters')]
    private string $title;

    #[ORM\Column(type: 'string', length: 255)]
    private string $slug;

    /** @var Collection<int, Travel> */
    #[ORM\OneToMany(targetEntity: Travel::class, mappedBy: 'categories', cascade: ['persist'])]
    private Collection $travel;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $url = null;

    public function __construct()
    {
        $this->travel = new ArrayCollection();
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
        $this->slug = $slugify->slugify($this->title);
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /*
    * returns the path to the file
    */

    /**
     * @return Collection<int, Travel>
     */
    public function getTravel(): Collection
    {
        return $this->travel;
    }

    public function addTravel(Travel $travel): self
    {
        if (!$this->travel->contains($travel)) {
            $this->travel[] = $travel;
            $travel->setCategories($this);
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->title;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getPicturename(): ?string
    {
        if (null === $this->url) {
            return null;
        }

        return '/data2/'.basename($this->url);
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }
}
