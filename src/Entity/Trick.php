<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\TrickRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\String\Slugger\AsciiSlugger;

/**
 * Class Trick
 * @package App\Entity
 * @ORM\Table(name="esvi_trick")
 * @ORM\Entity(repositoryClass=TrickRepository::class)
 */
final class Trick extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $mainPicture;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="tricks")
     */
    private $category;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function getMainPicture(): ?string
    {
        return $this->mainPicture;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function create(
        string $title,
        string $description,
        ?\DateTimeInterface $updatedAt,
        ?string $mainPicture): Trick
    {
        $this->title = $title;
        $slugger = new AsciiSlugger();
        $this->slug = strtolower($slugger->slug($this->title));
        $this->description = $description;
        $this->updatedAt = $updatedAt;
        $this->mainPicture = $mainPicture;

        return $this;
    }

    public function updateTitle(string $title): Trick
    {
        $this->title = $title;
        $this->updatedAt = new \DateTime('now');

        return $this;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }
}
