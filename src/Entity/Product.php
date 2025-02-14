<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(
        length: 255,
        nullable: false
    )]
    private string $name;

    #[ORM\Column(
        length: 255,
        nullable: true
    )]
    private string $description;

    #[ORM\Column(nullable: false)]
    private int $price;

    #[ORM\Column(nullable: false)]
    private bool $active = true;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updated_at = null;

    #[ORM\ManyToMany(
        targetEntity: Category::class,
        inversedBy: "products"
    )]
    #[ORM\JoinTable(name: "category_product")]
    private Collection $categories;

    #[ORM\OneToMany(
        targetEntity: Image::class,
        mappedBy: 'product',
        cascade: ['persist', 'remove']
    )]
    private Collection $images;


    public function __construct()
    {
        $this->categories = new ArrayCollection();
        $this->images = new ArrayCollection();
    }

    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function addCategory(Category $category): static
    {
        if (!$this->categories->contains($category)) {
            $this->categories[] = $category;
            $category->addProduct($this);
        }

        return $this;
    }

    public function removeCategory(Category $category): static
    {
        if ($this->categories->removeElement($category)) {
            $category->removeProduct($this);
        }

        return $this;
    }

    public function addImage(Image $image): static
    {
        if (!$this->images->contains($image)) {
            $this->images[] = $image;
            $image->setProduct($this);
        }

        return $this;
    }

    public function removeImage(Image $image): static
    {
        if ($this->images->removeElement($image)) {
            if ($image->getProduct() === $this) {
                $image->setProduct(null);
            }
        }

        return $this;
    }

    public function getImages(): Collection
    {
        return $this->images;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getActive(): bool
    {
        return $this->active;
    }

    public function getPrice(): int
    {
        return $this->price;
    }
}
