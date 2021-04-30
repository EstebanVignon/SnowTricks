<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="esvi_user")
 * @UniqueEntity(fields="username", message="Nom d'utilisateur déjà existant")
 * @UniqueEntity(fields="email", message="Cet email existe déjà")
 */
class User extends AbstractEntity implements UserInterface
{
    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Length(
     *     min=6,
     *     minMessage="Nom d'utilisateur trop court, minimum 6 caractères",
     *     max=20,
     *     maxMessage="Nom d'utilisateur trop long, maximum 20 caractères"
     * )
     */
    private string $username;

    /**
     * @ORM\Column(type="json")
     */
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Assert\Length(
     *     min=8,
     *     minMessage="mot de passe trop court, minimum 8 caractères",
     *     max=20,
     *     maxMessage="Mot de passe trop long, max 20 caractères"
     * )
     */
    private string $password;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(message="doit être un email")
     */
    private string $email;

    /**
     * @ORM\Column(type="boolean")
     */
    private bool $isActive;

    /**
     * @ORM\OneToMany(targetEntity=TokenHistory::class, mappedBy="user")
     */
    private ?Collection $tokensHistory;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user")
     */
    private ?Collection $comments;

    /**
     * @ORM\OneToMany(targetEntity=Trick::class, mappedBy="user")
     */
    private ?Collection $tricks;

    public function __construct()
    {
        parent::__construct();
        $this->tokensHistory = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->tricks = new ArrayCollection();
        $this->setIsActive(false);
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * @return Collection|TokenHistory[]
     */
    public function getTokensHistory(): Collection
    {
        return $this->tokensHistory;
    }

    public function addTokensHistory(TokenHistory $tokensHistory): self
    {
        if (!$this->tokensHistory->contains($tokensHistory)) {
            $this->tokensHistory[] = $tokensHistory;
            $tokensHistory->setUser($this);
        }

        return $this;
    }

    public function removeTokensHistory(TokenHistory $tokensHistory): self
    {
        if ($this->tokensHistory->removeElement($tokensHistory)) {
            // set the owning side to null (unless already changed)
            if ($tokensHistory->getUser() === $this) {
                $tokensHistory->setUser(null);
            }
        }

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Trick[]
     */
    public function getTricks(): Collection
    {
        return $this->tricks;
    }

    public function addTrick(Trick $trick): self
    {
        if (!$this->tricks->contains($trick)) {
            $this->tricks[] = $trick;
            $trick->setUser($this);
        }

        return $this;
    }

    public function removeTrick(Trick $trick): self
    {
        if ($this->tricks->removeElement($trick)) {
            // set the owning side to null (unless already changed)
            if ($trick->getUser() === $this) {
                $trick->setUser(null);
            }
        }

        return $this;
    }
}
