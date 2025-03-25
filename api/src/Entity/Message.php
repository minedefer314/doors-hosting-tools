<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Repository\MessageRepository;
use App\State\MessageStateProcessor;
use Carbon\Carbon;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ApiResource(
    shortName: "Messages",
    description: "Api of messages",
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['messages:read']],
        ),
        new Post(
            normalizationContext: ['groups' => ['messages:read']],
            denormalizationContext: ['groups' => ['messages:write']],
            processor: MessageStateProcessor::class,
        ),
    ],
    order: ['createdAt' => 'DESC'],
    paginationItemsPerPage: 20
)]

class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["messages:read", "messages:write"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["messages:read", "messages:write"])]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["messages:read"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["messages:read"])]
    private ?User $sender = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): static
    {
        $this->content = $content;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedAtAgo(): ?string
    {
        if($this->getCreatedAt())
        {
            return Carbon::instance($this->getCreatedAt())->diffForHumans();
        }
        return null;
    }

    public function getSender(): ?User
    {
        return $this->sender;
    }

    public function setSender(?User $sender): static
    {
        $this->sender = $sender;

        return $this;
    }
}
