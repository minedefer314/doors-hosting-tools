<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
#[ApiResource(
    shortName: "user",
    description: "Api of users",
    operations: [
    ]
)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["messages:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 5000, nullable: true)]
    private ?string $robloxToken = null;

    #[ORM\Column(type: "bigint")]
    #[Groups(["games:item:read", "games:item:wins:read"])]
    private ?int $robloxId = null;

    #[ORM\Column(length: 30)]
    #[Groups(["messages:read", "games:item:read", "games:item:wins:read", "games:read"])]
    private ?string $username = null;

    #[ORM\Column(length: 30)]
    #[Groups(["messages:read", "games:item:read", "games:item:wins:read", "games:read"])]
    private ?string $displayName = null;

    #[ORM\Column(length: 500)]
    #[Groups(["messages:read", "games:item:read", "games:item:wins:read", "games:read"])]
    private ?string $picture = null;

    #[ORM\Column(length: 255)]
    #[Groups(["messages:read", "games:item:read", "games:item:wins:read", "games:read"])]
    private ?string $profile = null;

    #[ORM\OneToMany(targetEntity: Game::class, mappedBy: 'host')]
    private Collection $games;

    #[ORM\OneToMany(targetEntity: Win::class, mappedBy: 'player')]
    private Collection $wins;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'sender')]
    private Collection $messages;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->wins = new ArrayCollection();
        $this->messages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRobloxToken(): ?string
    {
        return $this->robloxToken;
    }

    public function setRobloxToken(?string $robloxToken): static
    {
        $this->robloxToken = $robloxToken;

        return $this;
    }

    public function getRobloxId(): ?int
    {
        return $this->robloxId;
    }

    public function setRobloxId(int $robloxId): static
    {
        $this->robloxId = $robloxId;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(string $displayName): static
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    public function getProfile(): ?string
    {
        return $this->profile;
    }

    public function setProfile(string $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection<int, Game>
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): static
    {
        if (!$this->games->contains($game)) {
            $this->games->add($game);
            $game->setHost($this);
        }

        return $this;
    }

    public function removeGame(Game $game): static
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getHost() === $this) {
                $game->setHost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Win>
     */
    public function getWins(): Collection
    {
        return $this->wins;
    }

    public function addWin(Win $win): static
    {
        if (!$this->wins->contains($win)) {
            $this->wins->add($win);
            $win->setPlayer($this);
        }

        return $this;
    }

    public function removeWin(Win $win): static
    {
        if ($this->wins->removeElement($win)) {
            // set the owning side to null (unless already changed)
            if ($win->getPlayer() === $this) {
                $win->setPlayer(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setSender($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getSender() === $this) {
                $message->setSender(null);
            }
        }

        return $this;
    }
}
