<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

abstract class AbstractEntity
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     */
    protected string $id;

    /**
     * @ORM\Column(type="datetime")
     */
    protected \DateTime $createdAt;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->createdAt = new \DateTime("now");
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }
}
