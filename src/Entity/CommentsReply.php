<?php

namespace App\Entity;

use App\Repository\CommentsReplyRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommentsReplyRepository::class)]
class CommentsReply
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_posts_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    #[Groups(['api_posts_read'])]
    private ?string $User = 'Laurent Theneau';

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 2000)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['api_posts_read'])]
    private ?string $comment = null;

    #[ORM\Column]
    private ?bool $accepted = false;
    #[ORM\ManyToOne(inversedBy: 'comments')]
    private ?Posts $posts = null;

    #[ORM\Column]
    #[Groups(['api_posts_read'])]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?string
    {
        return $this->User;
    }

    public function setUser(string $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function isAccepted(): ?bool
    {
        return $this->accepted;
    }

    public function setAccepted(bool $accepted): static
    {
        $this->accepted = $accepted;

        return $this;
    }

    public function getPosts(): ?Posts
    {
        return $this->posts;
    }

    public function setPosts(?Posts $posts): static
    {
        $this->posts = $posts;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
