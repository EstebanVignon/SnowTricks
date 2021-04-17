<?php

namespace App\Entity;

use App\Repository\TokenHistoryRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TokenHistoryRepository::class)
 * @ORM\Table(name="esvi_token_history")
 */
class TokenHistory extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $type;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private string $value;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="tokensHistory")
     * @ORM\JoinColumn(nullable=false)
     */
    private ?User $user;

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
