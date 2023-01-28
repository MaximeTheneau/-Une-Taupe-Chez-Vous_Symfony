<?php

namespace App\Entity;

use App\Repository\FaqRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FaqRepository::class)]
#[ApiResource]
class Faq
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_faq_browse'])]
    private ?int $id = null;

    #[ORM\Column(length: 160)]
    #[Groups(['api_faq_browse'])]
    private ?string $question = null;

    #[ORM\Column(length: 500)]
    #[Groups(['api_faq_browse'])]
    private ?string $answer = null;

    #[ORM\Column]
    #[Groups(['api_faq_browse'])]
    private ?bool $open = false;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): self
    {
        $this->question = $question;

        return $this;
    }

    public function getAnswer(): ?string
    {
        return $this->answer;
    }

    public function setAnswer(string $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function isOpen(): ?bool
    {
        return $this->open ?? false;
    }

    public function setOpen(bool $open): self
    {
        $this->open = false;

        return $this;
    }
}
