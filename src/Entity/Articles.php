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
    
    #[ORM\Column(length: 255, unique: true, type: Types::STRING)]
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

    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'articles')]
    #[Groups(['api_articles_read'])]
    private Collection $category;
    private $newCategory;

    public function getNewCategory(): ?string
    {
        return $this->newCategory;
    }
    
    public function setNewCategory(?string $newCategory): self
    {
        $this->newCategory = $newCategory;
    
        return $this;
    }
    public function __construct()
    {
        $this->listArticles = new ArrayCollection();
        $this->paragraphArticles = new ArrayCollection();
        $this->category = new ArrayCollection();
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

    /**
     * @return Collection<int, Category>
     */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }


}