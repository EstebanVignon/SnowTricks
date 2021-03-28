<?php

namespace App\Entity;

use App\Repository\VideoRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Video
 * @package App\Entity
 * @ORM\Table(name="esvi_video")
 * @ORM\Entity(repositoryClass=VideoRepository::class)
 */
final class Video extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Url(message="Doit être une URL valide")
     */
    private string $link;

    /**
     * @ORM\ManyToOne(targetEntity=Trick::class, inversedBy="videos")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?Trick $trick;

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setLink(string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getTrick(): ?Trick
    {
        return $this->trick;
    }

    public function setTrick(?Trick $trick): self
    {
        $this->trick = $trick;

        return $this;
    }
}
