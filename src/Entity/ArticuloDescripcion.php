<?php

namespace App\Entity;

use App\Repository\ArticuloDescripcionRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="articulo_descripcion")
 * @ORM\Entity(repositoryClass=ArticuloDescripcionRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class ArticuloDescripcion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id",type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="descripcion", type="text")
     * @Assert\NotNull(message="Inserta una descripcion al articulo", groups={"backend_articulo_descripcion"})
     */
    private $descripcion;

    /**
     * @ORM\OneToOne(targetEntity=Articulo::class, inversedBy="articuloDescripcion", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $articulo;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaHoraCreacion;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $usuarioCreador;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     */
    private $usuarioEditor;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $fechaHoraEdicion;

    public function __construct()
    {
        $this->fechaHoraCreacion = new \DateTime();
    }

    /**
     * @ORM\PreUpdate()
     */
    public function atUpdate() {

        if(is_null($this->fechaHoraCreacion)){
            $this->fechaHoraCreacion = new \DateTime();
        }

        $this->setFechahoraEdicion = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getArticulo(): ?Articulo
    {
        return $this->articulo;
    }

    public function setArticulo(Articulo $articulo): self
    {
        $this->articulo = $articulo;

        return $this;
    }

    public function getFechaHoraCreacion(): ?\DateTimeInterface
    {
        return $this->fechaHoraCreacion;
    }

    public function setFechaHoraCreacion(\DateTimeInterface $fechaHoraCreacion): self
    {
        $this->fechaHoraCreacion = $fechaHoraCreacion;

        return $this;
    }

    public function getUsuarioCreador(): ?User
    {
        return $this->usuarioCreador;
    }

    public function setUsuarioCreador(?User $usuarioCreador): self
    {
        $this->usuarioCreador = $usuarioCreador;

        return $this;
    }

    public function getUsuarioEditor(): ?User
    {
        return $this->usuarioEditor;
    }

    public function setUsuarioEditor(?User $usuarioEditor): self
    {
        $this->usuarioEditor = $usuarioEditor;

        return $this;
    }

    public function getFechaHoraEdicion(): ?\DateTimeInterface
    {
        return $this->fechaHoraEdicion;
    }

    public function setFechaHoraEdicion(?\DateTimeInterface $fechaHoraEdicion): self
    {
        $this->fechaHoraEdicion = $fechaHoraEdicion;

        return $this;
    }
}
