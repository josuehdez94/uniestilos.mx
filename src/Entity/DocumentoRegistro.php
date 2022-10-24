<?php

namespace App\Entity;

use App\Repository\DocumentoRegistroRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="DocumentoRegistro", uniqueConstraints={
 *  @ORM\UniqueConstraint(name="articulo_talla_UNIQUE", columns={"articulo_id", "articulo_talla_id", "documento_id"})
 * })
 * @ORM\Entity(repositoryClass=DocumentoRegistroRepository::class)
 */
class DocumentoRegistro
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="fecha_hora_creacion", type="datetime")
     */
    private $fechaHoraCreacion;

    /**
     * @ORM\Column(name="cantidad", type="integer")
     * @Assert\NotBlank(
     *  message="Selecciona una cantidad",
     *  groups={"front_agregar_articulo_carrito"}
     * )
     * @Assert\Type(
     *     type="integer",
     *     message="El valor {{ value }} no es valido {{ type }}.",
     *     groups={"front_agregar_articulo_carrito"}
     * )
     */
    private $cantidad;

    /**
     * @ORM\Column(name="precio", type="decimal", precision=10, scale=2)
     * @Assert\NotBlank(
     *  message="Selecciona un precio",
     *  groups={"front_agregar_articulo_carrito"}
     * )
     */
    private $precio;


    /**
     * @ORM\Column(name="total", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $total;

    /**
     * @ORM\ManyToOne(targetEntity=Articulo::class, inversedBy="documentoRegistros")
     * @ORM\JoinColumn(name="articulo_id", nullable=false)
     */
    private $articulo;

    /**
     * @ORM\ManyToOne(targetEntity=Documento::class, inversedBy="registros")
     * @ORM\JoinColumn(name="documento_id", nullable=false)
     */
    private $Documento;

    /**
     * @ORM\Column(name="descuento", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $descuento;

    /**
     * @ORM\Column(name="promocion", type="boolean", nullable=true)
     */
    private $promocion;

    /**
     * @ORM\ManyToOne(targetEntity=ArticuloTalla::class)
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(
     *  message="Selecciona una talla",
     *  groups={"front_agregar_articulo_carrito"}
     * )
     */
    private $articuloTalla;

    public function __construct()
    {
        $this->fechaHoraCreacion = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getCantidad()
    {
        return $this->cantidad;
    }

    public function setCantidad($cantidad)
    {
        $this->cantidad = $cantidad;

        return $this;
    }

    public function getPrecio()
    {
        return $this->precio;
    }

    public function setPrecio($precio)
    {
        $this->precio = $precio;

        return $this;
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    public function getArticulo()
    {
        return $this->articulo;
    }

    public function setArticulo($articulo)
    {
        $this->articulo = $articulo;

        return $this;
    }

    public function getDocumento()
    {
        return $this->Documento;
    }

    public function setDocumento($Documento)
    {
        $this->Documento = $Documento;

        return $this;
    }

    public function getDescuento()
    {
        return $this->descuento;
    }

    public function setDescuento($descuento)
    {
        $this->descuento = $descuento;

        return $this;
    }

    public function getPromocion()
    {
        return $this->promocion;
    }

    public function setPromocion($promocion)
    {
        $this->promocion = $promocion;

        return $this;
    }

    public function getArticuloTalla(): ?ArticuloTalla
    {
        return $this->articuloTalla;
    }

    public function setArticuloTalla(?ArticuloTalla $articuloTalla): self
    {
        $this->articuloTalla = $articuloTalla;

        return $this;
    }
}
