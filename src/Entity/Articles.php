<?php

namespace App\Entity;

use App\Repository\ArticlesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;


#[ORM\Entity(repositoryClass: ArticlesRepository::class)]
class Articles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_articles_browse', 'api_articles_read', 'api_articles_desc' ])]
    private ?int $id = null;

    #[ORM\Column(length: 70, unique: true, type: Types::STRING)]
    #[Groups(['api_articles_browse', 'api_articles_read', 'api_articles_desc' ])]
    private ?string $title = null;
    
    #[ORM\Column(length: 70, unique: true, type: Types::STRING)]
    #[Groups(['api_articles_browse', 'api_articles_read', 'api_articles_desc'])]
    private ?string $slug = null;

    
    #[ORM\Column(length: 5000, nullable: true, type: Types::STRING)]
    #[Type(type: Types::string)]
    #[Groups(['api_articles_read'])]
    private ?string $contents = null;

    #[ORM\Column]
    #[Groups(['api_articles_read'])]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['api_articles_read'])]
    private ?\DateTime $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'articles', targetEntity: ListArticles::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['api_articles_read'])]
    private Collection $listArticles;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $links = null;

    #[ORM\OneToMany(mappedBy: 'articles', targetEntity: ParagraphArticles::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['api_articles_read'])]
    private Collection $paragraphArticles;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $textLinks = null;

    #[ORM\ManyToOne(inversedBy: 'articles')]
    #[Groups(['api_articles_read'])]
    private ?Category $category = null;

    #[ORM\ManyToMany(targetEntity: Subcategory::class, inversedBy: 'articles')]
    #[Groups(['api_articles_read'])]
    private Collection $subcategory;

    #[ORM\ManyToMany(targetEntity: Subtopic::class, inversedBy: 'articles')]
    #[Groups(['api_articles_read'])]
    private Collection $subtopic;

    #[ORM\Column(length: 125, nullable: true)]
    private ?string $altImg = null;

    #[ORM\Column(length: 500, nullable: true)]
    private ?string $imgPost = null;

    public function __construct()
    {
        $this->listArticles = new ArrayCollection();
        $this->paragraphArticles = new ArrayCollection();
        $this->subcategory = new ArrayCollection();
        $this->subtopic = new ArrayCollection();
    }

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

    public function getContents(): ?string
    {
        return $this->contents;
    }

    public function setContents(string $contents): self
    {
        $this->contents = $contents;

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


    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTime $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * @return Collection<int, ListArticles>
     */
    public function getListArticles(): Collection
    {
        return $this->listArticles;
    }

    public function addListArticle(ListArticles $listArticle): self
    {
        if (!$this->listArticles->contains($listArticle)) {
            $this->listArticles->add($listArticle);
            $listArticle->setArticles($this);
        }

        return $this;
    }

    public function removeListArticle(ListArticles $listArticle): self
    {
        if ($this->listArticles->removeElement($listArticle)) {
            // set the owning side to null (unless already changed)
            if ($listArticle->getArticles() === $this) {
                $listArticle->setArticles(null);
            }
        }

        return $this;
    }

    public function getLinks(): ?string
    {
        return $this->links;
    }

    public function setLinks(?string $links): self
    {
        $this->links = $links;

        return $this;
    }

    /**
     * @return Collection<int, ParagraphArticles>
     */
    public function getParagraphArticles(): Collection
    {
        return $this->paragraphArticles;
    }

    public function addParagraphArticle(ParagraphArticles $paragraphArticle): self
    {
        if (!$this->paragraphArticles->contains($paragraphArticle)) {
            $this->paragraphArticles->add($paragraphArticle);
            $paragraphArticle->setArticles($this);
        }

        return $this;
    }

    public function removeParagraphArticle(ParagraphArticles $paragraphArticle): self
    {
        if ($this->paragraphArticles->removeElement($paragraphArticle)) {
            // set the owning side to null (unless already changed)
            if ($paragraphArticle->getArticles() === $this) {
                $paragraphArticle->setArticles(null);
            }
        }

        return $this;
    }

    public function getTextLinks(): ?string
    {
        return $this->textLinks;
    }

    public function setTextLinks(?string $textLinks): self
    {
        $this->textLinks = $textLinks;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection<int, Subcategory>
     */
    public function getSubcategory(): Collection
    {
        return $this->subcategory;
    }

    public function addSubcategory(Subcategory $subcategory): self
    {
        if (!$this->subcategory->contains($subcategory)) {
            $this->subcategory->add($subcategory);
        }

        return $this;
    }

    public function removeSubcategory(Subcategory $subcategory): self
    {
        $this->subcategory->removeElement($subcategory);

        return $this;
    }

    /**
     * @return Collection<int, Subtopic>
     */
    public function getSubtopic(): Collection
    {
        return $this->subtopic;
    }

    public function addSubtopic(Subtopic $subtopic): self
    {
        if (!$this->subtopic->contains($subtopic)) {
            $this->subtopic->add($subtopic);
        }

        return $this;
    }

    public function removeSubtopic(Subtopic $subtopic): self
    {
        $this->subtopic->removeElement($subtopic);

        return $this;
    }

    public function getAltImg(): ?string
    {
        return $this->altImg;
    }

    public function setAltImg(?string $altImg): self
    {
        $this->altImg = $altImg;

        return $this;
    }

    public function getImgPost(): ?string
    {
        return $this->imgPost;
    }

    public function setImgPost(?string $imgPost): self
    {
        $this->imgPost = $imgPost;

        return $this;
    }


}