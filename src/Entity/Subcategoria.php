<?php

namespace App\Entity;

use App\Repository\SubcategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use App\Generales\Funciones;

/**
 * @ORM\Table(name="subcategoria", uniqueConstraints={
 *  @ORM\UniqueConstraint(name="nombre_UNIQUE", columns={"nombre", "categoria_id"})
 * })
 * @ORM\Entity(repositoryClass=SubcategoriaRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity(
 *     fields={"nombre", "categoria"},
 *     errorPath="nombre",
 *     message="El nombre de la subcategoria ya existe en la categoria.",
 *     groups={"backend_subcategoria_nueva", "backend_subcategoria_editar"}
 * )
 */
class Subcategoria
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Categoria::class, inversedBy="subcategorias")
     * @ORM\JoinColumn(nullable=false)
     */
    private $categoria;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank(
     *  message="Ingresa un nombre para la subcategoria",
     *  groups={"backend_subcategoria_nueva", "backend_subcategoria_editar"}
     * )
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     *      minMessage = "EL nombre debe contener minimo {{ limit }} caracteres",
     *      maxMessage = "El nombre debe contener maximo {{ limit }} caracteres",
     *      groups={"backend_subcategoria_nueva", "backend_subcategoria_editar"}
     * )
     */
    private $nombre;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $urlAmigable;

    /**
     * @ORM\OneToMany(targetEntity=Articulo::class, mappedBy="subcategoria")
     */
    private $articulos;

    public function __construct()
    {
        $this->articulos = new ArrayCollection();
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function atUpdate() {
        $utilerias = new Funciones();
        $this->urlAmigable = $utilerias->urlAmigable($this->nombre);
    }

    public function __toString()
    {
        return $this->nombre;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoria(): ?Categoria
    {
        return $this->categoria;
    }

    public function setCategoria(?Categoria $categoria): self
    {
        $this->categoria = $categoria;

        return $this;
    }

    public function getNombre(): ?string
    {
        return $this->nombre;
    }

    public function setNombre(string $nombre): self
    {
        $this->nombre = $nombre;

        return $this;
    }

    public function getUrlAmigable(): ?string
    {
        return $this->urlAmigable;
    }

    public function setUrlAmigable(?string $urlAmigable): self
    {
        $this->urlAmigable = $urlAmigable;

        return $this;
    }

    /**
     * @return Collection|Articulo[]
     */
    public function getArticulos(): Collection
    {
        return $this->articulos;
    }

    public function addArticulo(Articulo $articulo): self
    {
        if (!$this->articulos->contains($articulo)) {
            $this->articulos[] = $articulo;
            $articulo->setSubcategoria($this);
        }

        return $this;
    }

    public function removeArticulo(Articulo $articulo): self
    {
        if ($this->articulos->removeElement($articulo)) {
            // set the owning side to null (unless already changed)
            if ($articulo->getSubcategoria() === $this) {
                $articulo->setSubcategoria(null);
            }
        }

        return $this;
    }
}
