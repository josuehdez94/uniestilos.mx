<?php

namespace App\Entity;

use App\Repository\CategoriaRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Generales\Funciones;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="Categoria")
 * @ORM\Entity(repositoryClass=CategoriaRepository::class)
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("nombre", message="Ya existe una categoria con este nombre", groups={"backend_categoria_nueva", "backend_categoria_editar"})
 */
class Categoria
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="nombre", type="string", unique=true, length=50)
     * @Assert\NotBlank(
     *  message="Ingresa un nombre para la categoria",
     *  groups={"backend_categoria_nueva", "backend_categoria_editar"}
     * )
     * @Assert\Length(
     *      min = 3,
     *      max = 50,
     *      minMessage = "EL nombre debe contener minimo {{ limit }} caracteres",
     *      maxMessage = "El nombre debe contener maximo {{ limit }} caracteres",
     *      groups={"backend_categoria_nueva", "backend_categoria_editar"}
     * )
     */
    private $nombre;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_creo_id", referencedColumnName="id")
     * })
     */
    private $usuarioCreador;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_edito_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $usuarioEditor;

    /**
     * @ORM\Column(name="fecha_hora_creacion", type="datetime")
     */
    private $fechahoraCreacion;

    /**
     * @ORM\Column(name="fecha_hora_edicion", type="datetime", nullable=true)
     */
    private $fechahoraEdicion;

    /**
     * @ORM\OneToMany(targetEntity=Subcategoria::class, mappedBy="categoria", orphanRemoval=true)
     */
    private $subcategorias;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $urlAmigable;

    public function __construct()
    {
        $this->fechahoraCreacion = new \DateTime();
        $this->subcategorias = new ArrayCollection();
        $utilerias = new Funciones();
        $this->urlAmigable = $utilerias->urlAmigable($this->nombre);
    }

    public function __toString() {
        return $this->nombre;
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function atUpdate() {

        if(is_null($this->fechahoraCreacion)){
            $this->fechahoraCreacion = new \DateTime();
        }
        $this->setFechahoraEdicion(new \DateTime());
        $utilerias = new Funciones();
        $this->urlAmigable = $utilerias->urlAmigable($this->nombre);
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return \User
     */
    public function getUsuarioCreador()
    {
        return $this->usuarioCreador;
    }

    /**
     * @param \User $usuarioCreador
     */
    public function setUsuarioCreador($usuarioCreador): void
    {
        $this->usuarioCreador = $usuarioCreador;
    }

    /**
     * @return \User
     */
    public function getUsuarioEditor()
    {
        return $this->usuarioEditor;
    }

    /**
     * @param \User $usuarioEditor
     */
    public function setUsuarioEditor($usuarioEditor): void
    {
        $this->usuarioEditor = $usuarioEditor;
    }

    /**
     * @return \DateTime
     */
    public function getFechahoraCreacion(): \DateTime
    {
        return $this->fechahoraCreacion;
    }

    /**
     * @param \DateTime $fechahoraCreacion
     */
    public function setFechahoraCreacion(\DateTime $fechahoraCreacion): void
    {
        $this->fechahoraCreacion = $fechahoraCreacion;
    }

    /**
     * @return mixed
     */
    public function getFechahoraEdicion()
    {
        return $this->fechahoraEdicion;
    }

    /**
     * @param mixed $fechahoraEdicion
     */
    public function setFechahoraEdicion($fechahoraEdicion): void
    {
        $this->fechahoraEdicion = $fechahoraEdicion;
    }

    /**
     * @return Collection|Subcategoria[]
     */
    public function getSubcategorias(): Collection
    {
        return $this->subcategorias;
    }

    public function addSubcategoria(Subcategoria $subcategoria): self
    {
        if (!$this->subcategorias->contains($subcategoria)) {
            $this->subcategorias[] = $subcategoria;
            $subcategoria->setCategoria($this);
        }

        return $this;
    }

    public function removeSubcategoria(Subcategoria $subcategoria): self
    {
        if ($this->subcategorias->removeElement($subcategoria)) {
            // set the owning side to null (unless already changed)
            if ($subcategoria->getCategoria() === $this) {
                $subcategoria->setCategoria(null);
            }
        }

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
}
