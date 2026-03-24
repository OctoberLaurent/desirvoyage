<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\PicturesRepository::class)]
final class Pictures
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 80, nullable: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 255)]
    private $url;

    #[ORM\ManyToOne(targetEntity: Travel::class, inversedBy: 'pictures', cascade: ['persist'])]
    private $travel;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /*
     * returns the web path to the file
     */
    public function getPicturename(): ?string
    {
        if (null === $this->url) {
            return null;
        }

        return '/data2/'.basename($this->url);
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getTravel(): ?Travel
    {
        return $this->travel;
    }

    public function setTravel(?Travel $travel): self
    {
        $this->travel = $travel;

        return $this;
    }

    public function __toString()
    {
        return $this->name;
    }
}
