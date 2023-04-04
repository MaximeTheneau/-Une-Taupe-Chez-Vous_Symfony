<?php

namespace App\Entity;

use App\Repository\PagesRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PagesRepository::class)]
#[ApiResource(
    order: ['createdAt' => 'DESC'],
    paginationEnabled: false,
)]
#[ApiFilter(SearchFilter::class, properties: ['conference' => 'exact'])]
class Pages
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_pages_browse', 'api_pages_read'])]
    private ?int $id = null;

    #[ORM\Column(length: 70)]
    #[Groups(['api_pages_browse', 'api_pages_read'])]
    private ?string $title = null;

    #[ORM\Column( nullable: true)]
    #[Assert\Length(
        max: 170,
        maxMessage: 'rerer {{ limit }} characters',
    )]
    #[Groups(['api_pages_read'])]
    private ?string $subtitle = null;

    #[ORM\Column(length: 1000)]
    #[Groups(['api_pages_read'])]
    private ?string $contents = null;

    #[ORM\Column(length: 750, nullable: true)]
    #[Groups(['api_pages_read'])]
    private ?string $contents2 = null;

    #[ORM\Column(length: 255)]
    #[Groups(['api_pages_browse', 'api_pages_read'])]
    private ?string $slug = null;

    #[ORM\Column()]
    private ?array $imgHeader = null;

    #[ORM\Column()]
    private ?string $imgHeaderJpg = null;

    #[ORM\Column(nullable: true)]
    private ?array $imgHeader2 = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
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

    public function getContents(): ?string
    {
        return $this->contents;
    }

    public function setContents(string $contents): self
    {
        $this->contents = $contents;

        return $this;
    }

    public function getContents2(): ?string
    {
        return $this->contents2;
    }

    public function setContents2(?string $contents2): self
    {
        $this->contents2 = $contents2;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getImgHeader(): ?array
    {
        $imgHeader = $this->imgHeader;

        return  $imgHeader;
    }

    public function setImgHeader(array $imgHeader): self
    {
        $this->imgHeader = $imgHeader;

        return $this;
    }


    public function getImgHeaderJpg(): ?string
    {
        return $this->imgHeaderJpg;
    }

    public function setImgHeaderJpg(string $imgHeaderJpg): self
    {
        $this->imgHeaderJpg = $imgHeaderJpg;

        return $this;
    }

    public function getImgHeader2(): array
    {
        return $this->imgHeader2;
    }

    public function setImgHeader2(?array $imgHeader2): self
    {
        $this->imgHeader2 = $imgHeader2;

        return $this;
    }
}