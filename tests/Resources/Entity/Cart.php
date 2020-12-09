<?php

namespace Trikoder\JsonApiBundle\Tests\Resources\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cart
 *
 * @ORM\Entity
 * @ORM\Table(name="cart")
 */
class Cart
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="carts")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="cart")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    /**
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products[] = $product;
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }

        return $this;
    }
}
