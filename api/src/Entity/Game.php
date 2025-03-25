<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\GameRepository;
use App\State\GameStateProcessor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: GameRepository::class)]
#[ApiResource(
    shortName: "Games",
    description: "Api of games",
    operations: [
        new Get(
            normalizationContext: ['groups' => ['games:item:read']],
        ),
        new Get(
            uriTemplate: "/games/{id}/wins",
            normalizationContext: ['groups' => ['games:item:wins:read']],
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['games:read']],
        ),
        new Post(
            normalizationContext: ['groups' => ['games:read']],
            denormalizationContext: ['groups' => ['games:write']],
            processor: GameStateProcessor::class,
        ),
    ]
)]
class Game
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["games:item:read", "games:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Please specify a title.")]
    #[Groups(["games:item:read", "games:read", "games:write"])]
    private ?string $title = null;

    #[ORM\ManyToOne(inversedBy: 'games')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["games:item:read", "games:read"])]
    private ?User $host = null;

    #[ORM\OneToMany(targetEntity: Win::class, mappedBy: 'game')]
    #[Groups(["games:item:wins:read"])]
    private Collection $wins;

    #[ORM\Column]
    #[Groups(["games:item:read", "games:write"])]
    private array $rules = [];

    #[ORM\Column(length: 5000)]
    #[Assert\NotBlank(message: "Please specify a description.")]
    #[Groups(["games:item:read", "games:write"])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["games:item:read"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["games:item:read"])]
    private ?\DateTime $lastActive = null;

    public function __construct()
    {
        $this->wins = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->lastActive = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getHost(): ?User
    {
        return $this->host;
    }

    public function setHost(?User $host): static
    {
        $this->host = $host;

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
            $win->setGame($this);
        }

        return $this;
    }

    public function removeWin(Win $win): static
    {
        if ($this->wins->removeElement($win)) {
            // set the owning side to null (unless already changed)
            if ($win->getGame() === $this) {
                $win->setGame(null);
            }
        }

        return $this;
    }

    public function getRules(): array
    {
        return $this->rules;
    }

    public function setRules(array $rules): static
    {
        $this->rules = $rules;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function updateLastActive(): static
    {
        $this->lastActive = new \DateTime();

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getLastActive(): ?\DateTime
    {
        return $this->lastActive;
    }
}
