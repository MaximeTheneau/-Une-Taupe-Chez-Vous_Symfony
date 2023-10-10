<?php

namespace App\Entity;

use App\Repository\PostsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\DateFilter;


#[ORM\Entity(repositoryClass: PostsRepository::class)]
#[ApiRessource(
    normalizationContext: ['groups' => ['api_posts_keyword']],
)]
class Posts
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_posts_browse', 'api_posts_read', 'api_posts_desc', 'api_posts_category', 'api_posts_keyword' ])]
    private ?int $id = null;
    
    #[ORM\Column(length: 70, unique: true, type: Types::STRING)]
    #[Groups(['api_posts_browse', 'api_posts_read', 'api_posts_desc', 'api_posts_category', 'api_posts_subcategory', 'api_posts_articles_desc', 'api_posts_all', 'api_posts_keyword' ])]
    private ?string $title = null;

    #[ORM\Column(length: 135)]
    #[Groups(['api_posts_read'])]
    private ?string $metaDescription = null;
    
    #[ORM\Column(length: 70, unique: true, type: Types::STRING)]
    #[Groups(['api_posts_browse', 'api_posts_read', 'api_posts_desc', 'api_posts_category', 'api_posts_subcategory', 'api_posts_all', 'api_posts_keyword' ])]
    private ?string $slug = null;

    
    #[ORM\Column(length: 5000, nullable: true, type: Types::STRING)]
    #[Type(type: Types::string)]
    #[Groups(['api_posts_read'])]
    private ?string $contents = null;

    #[ORM\Column]
    #[Groups(['api_posts_read', 'api_posts_category' ])]
    private ?\DateTime $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['api_posts_read', 'api_posts_category'])]
    private ?\DateTime $updatedAt = null;

    #[ORM\Column(length: 255)]
    #[Groups(['api_posts_read'])]
    private ?string $formattedDate = null;
    
    #[ORM\OneToMany(mappedBy: 'posts', targetEntity: ListPosts::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['api_posts_read'])]
    private Collection $listPosts;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $links = null;

    #[ORM\OneToMany(mappedBy: 'posts', targetEntity: ParagraphPosts::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['api_posts_read' ])]
    private Collection $paragraphPosts;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $textLinks = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[Groups(['api_posts_read', 'api_posts_category', 'api_posts_all', 'api_posts_desc', 'api_posts_subcategory', 'api_posts_category','api_posts_keyword' ])]
    private ?Category $category = null;

    #[ORM\Column(length: 125, nullable: true)]
    #[Groups(['api_posts_category', 'api_posts_read' ])]
    private ?string $altImg = null;

    #[ORM\Column(length: 500, nullable: true)]
    #[Groups(['api_posts_read', 'api_posts_all',  'api_posts_desc', 'api_posts_subcategory', 'api_posts_category', 'api_posts_keyword' ])]
    private ?string $imgPost = null;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    #[Groups(['api_posts_all', 'api_posts_browse', 'api_posts_category', 'api_posts_desc', 'api_posts_subcategory', 'api_posts_read', 'api_posts_keyword'])]
    private ?Subcategory $subcategory = null;

    #[ORM\OneToMany(mappedBy: 'posts', targetEntity: Comments::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[Groups(['api_posts_read'])]
    private Collection $comments;

    #[ORM\ManyToMany(targetEntity: Keyword::class, mappedBy: 'posts')]
    #[Groups(['api_posts_read'])]
    private Collection $keywords;


    public function __construct()
    {
        $this->listPosts = new ArrayCollection();
        $this->paragraphPosts = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->keywords = new ArrayCollection();
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
     * @return Collection<int, ListPosts>
     */
    public function getListPosts(): Collection
    {
        return $this->listPosts;
    }

    public function addListPost(ListPosts $listPost): self
    {
        if (!$this->listPosts->contains($listPost)) {
            $this->listPosts->add($listPost);
            $listPost->setPosts($this);
        }

        return $this;
    }

    public function removeListPost(ListPosts $listPost): self
    {
        if ($this->listPosts->removeElement($listPost)) {
            // set the owning side to null (unless already changed)
            if ($listPost->getPosts() === $this) {
                $listPost->setPosts(null);
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
     * @return Collection<int, ParagraphPosts>
     */
    public function getParagraphPosts(): Collection
    {
        return $this->paragraphPosts;
    }

    public function addParagraphPost(ParagraphPosts $paragraphPost): self
    {
        if (!$this->paragraphPosts->contains($paragraphPost)) {
            $this->paragraphPosts->add($paragraphPost);
            $paragraphPost->setPosts($this);
        }

        return $this;
    }

    public function removeParagraphPost(ParagraphPosts $paragraphPost): self
    {
        if ($this->paragraphPosts->removeElement($paragraphPost)) {
            // set the owning side to null (unless already changed)
            if ($paragraphPost->getPosts() === $this) {
                $paragraphPost->setPosts(null);
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

    public function getSubcategory(): ?Subcategory
    {
        return $this->subcategory;
    }

    public function setSubcategory(?Subcategory $subcategory): self
    {
        $this->subcategory = $subcategory;

        return $this;
    }

    /**
     * @return Collection<int, Comments>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comments $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPosts($this);
        }

        return $this;
    }

    public function removeComment(Comments $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPosts() === $this) {
                $comment->setPosts(null);
            }
        }

        return $this;
    }

    public function setComments(Collection $comments): static
    {
        $this->comments = $comments;

        return $this;
    }

    /**
     * @return Collection<int, Keyword>
     */
    public function getKeywords(): Collection
    {
        return $this->keywords;
    }

    public function addKeyword(Keyword $keyword): static
    {
        if (!$this->keywords->contains($keyword)) {
            $this->keywords->add($keyword);
            $keyword->addPost($this);
        }

        return $this;
    }

    public function removeKeyword(Keyword $keyword): static
    {
        if ($this->keywords->removeElement($keyword)) {
            $keyword->removePost($this);
        }

        return $this;
    }

    public function getMetaDescription(): ?string
    {
        return $this->metaDescription;
    }

    public function setMetaDescription(string $metaDescription): static
    {
        $this->metaDescription = $metaDescription;

        return $this;
    }

    public function getFormattedDate(): ?string
    {
        return $this->formattedDate;
    }

    public function setFormattedDate(string $formattedDate): static
    {
        $this->formattedDate = $formattedDate;

        return $this;
    }
}