<?php

namespace App\Entity;

use App\Repository\ParagraphArticlesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;


#[ORM\Entity(repositoryClass: ParagraphArticlesRepository::class)]
class ParagraphArticles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 170, nullable: true)]
    #[Groups(['api_articles_read'])]
    private ?string $subtitle = null;

    #[ORM\Column(length: 5000, nullable: true)]
    #[Groups(['api_articles_read'])]
    private ?string $paragraph = null;

    #[ORM\ManyToOne(inversedBy: 'paragraphArticles')]
    private ?Articles $articles = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['api_articles_read'])]
    private ?string $imgPostParagh = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubtitle(): ?string
    {
        return $this->subtitle;
    }

    public function setSubtitle(?string $subtitle): self
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    public function getParagraph(): ?string
    {
        return $this->paragraph;
    }

    public function setParagraph(?string $paragraph): self
    {
        $this->paragraph = $paragraph;

        return $this;
    }

    public function getArticles(): ?Articles
    {
        return $this->articles;
    }

    public function setArticles(?Articles $articles): self
    {
        $this->articles = $articles;

        return $this;
    }

    public function getImgPostParagh(): ?string
    {
        return $this->imgPostParagh;
    }

    public function setImgPostParagh(?string $imgPostParagh): self
    {
        $this->imgPostParagh = $imgPostParagh;

        return $this;
    }
}
