<?php

namespace App\Entity;

use App\Repository\ArticuloRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="Articulo",
 *  indexes={
 *  @ORM\Index(name="description", columns={"descripcion"}, flags={"fulltext"}),
 *  @ORM\Index(name="code", columns={"sku"}, flags={"fulltext"}),
 *  @ORM\Index(name="stringBusqueda", columns={"sku", "descripcion"}, flags={"fulltext"})
 *  }
 * )
 * @ORM\Entity(repositoryClass=ArticuloRepository::class)
 * @UniqueEntity("sku", message="Este sku ya existe")
 * @UniqueEntity("url_amigable")
 * @ORM\HasLifecycleCallbacks()
 */
class Articulo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id",type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="sku", type="string", length=30, unique=true)
     * @Assert\NotNull(groups={"backend_articulo_nuevo", "backend_articulo_editar"})
     * @Assert\Unique
     */
    private $sku;

    /**
     * @ORM\Column(name="precio1", type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\Type(
     *     type="integer",
     *     message="EL valor {{ value }} no es valido {{ type }}."
     * )
     */
    private $precio1;

    /**
     * @ORM\Column(name="precio2", type="decimal", precision=10, scale=2, nullable=true)
     * @Assert\Type(
     *     type="integer",
     *     message="EL valor {{ value }} no es valido {{ type }}."
     * )
     */
    private $precio2;

    /**
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

    /**
     * @ORM\Column(name="descripcion", type="string", length=255)
     * @Assert\NotNull(groups={"backend_articulo_nuevo", "backend_articulo_editar"})
     * @Assert\Length(
     *      min = 10,
     *      max = 255,
     *      minMessage = "La descripcion debe contener minimo {{ limit }} caracteres",
     *      maxMessage = "La descripcion debe contener maximo {{ limit }} caracteres",
     *      allowEmptyString = false,
     *      groups={"backend_articulo_nuevo", "backend_articulo_editar"}
     * )
     */
    private $descripcion;

    /**
     * @ORM\Column(name="url_amigable", type="string", length=255, unique=true)
     * @Assert\Unique
     */
    private $urlAmigable;

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
     * @var \ArticuloFotografia
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\ArticuloFotografia")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="fotografia_principal_id", referencedColumnName="id", nullable=true)
     * })
     */
    private $fotografiaPrincipal;

    /**
     * @ORM\Column(name="fecha_hora_creacion", type="datetime")
     */
    private $fechahoraCreacion;

    /**
     * @ORM\Column(name="fecha_hora_edicion", type="datetime", nullable=true)
     */
    private $fechahoraEdicion;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ArticuloFotografia", mappedBy="articulo")
     */
    private $fotografias;

    /**
     * @ORM\OneToMany(targetEntity=DocumentoRegistro::class, mappedBy="articulo")
     */
    private $documentoRegistros;

    /**
     * @ORM\OneToOne(targetEntity=ArticuloDescripcion::class, mappedBy="articulo", cascade={"persist", "remove"})
     */
    private $articuloDescripcion;

    /**
     * @ORM\ManyToOne(targetEntity=Subcategoria::class, inversedBy="articulos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subcategoria;

    /**
     * @ORM\OneToMany(targetEntity=ArticuloTalla::class, mappedBy="articulo", orphanRemoval=true)
     */
    private $tallas;

    /**
     * @ORM\Column(type="boolean")
     */
    private $sobrePedido = true;

    public function __construct()
    {
        $this->fechahoraCreacion = new \DateTime();
        $this->documentoRegistros = new ArrayCollection();
        $this->tallas = new ArrayCollection();
    }

    public function __toString(){
        return $this->descripcion;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku()
    {
        return $this->sku;
    }

    public function setSku($sku)
    {
        $this->sku = $sku;

        return $this;
    }

    public function getPrecio1(): ?string
    {
        return $this->precio1;
    }

    public function setPrecio1(?string $precio1): self
    {
        $this->precio1 = $precio1;

        return $this;
    }

    public function getPrecio2(): ?string
    {
        return $this->precio2;
    }

    public function setPrecio2(?string $precio2): self
    {
        $this->precio2 = $precio2;

        return $this;
    }

    public function getActivo(): ?bool
    {
        return $this->activo;
    }

    public function setActivo(?bool $activo): self
    {
        $this->activo = $activo;

        return $this;
    }

    public function getDescripcion()
    {
        return $this->descripcion;
    }

    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    public function getUrlAmigable()
    {
        return $this->urlAmigable;
    }

    public function setUrlAmigable($urlAmigable)
    {
        $this->urlAmigable = $urlAmigable;

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
     * @return mixed
     */
    public function getFechahoraCreacion()
    {
        return $this->fechahoraCreacion;
    }

    /**
     * @param mixed $fechahoraCreacion
     */
    public function setFechahoraCreacion($fechahoraCreacion): void
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
     * @return mixed
     */
    public function getFotografias()
    {
        return $this->fotografias;
    }

    /**
     * Get the value of fotografiaPrincipal
     *
     * @return  \ArticuloFotografia
     */ 
    public function getFotografiaPrincipal()
    {
        return $this->fotografiaPrincipal;
    }

    /**
     * Set the value of fotografiaPrincipal
     *
     * @param  \ArticuloFotografia  $fotografiaPrincipal
     *
     * @return  self
     */ 
    public function setFotografiaPrincipal($fotografiaPrincipal)
    {
        $this->fotografiaPrincipal = $fotografiaPrincipal;

        return $this;
    }

    /**
     * @param mixed $fotografias
     */
    public function setFotografias($fotografias): void
    {
        $this->fotografias = $fotografias;
    }

    /**
     * @ORM\PreUpdate()
     */
    public function atUpdate() {

        if(is_null($this->fechahoraCreacion)){
            $this->fechahoraCreacion = new \DateTime();
        }

        $this->setFechahoraEdicion(new \DateTime());
    }

    /**
     * funcion para agregar la urlAmigable a los articulos
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function addUrlAmigable()
    {
        $url = new \App\Generales\Funciones();
        $this->urlAmigable = $url->urlAmigable($this->id . "-" .$this->descripcion. "-". $this->sku);
        return $this;
    }

    /**
     * @return Collection|DocumentoRegistro[]
     */
    public function getDocumentoRegistros(): Collection
    {
        return $this->documentoRegistros;
    }

    public function addDocumentoRegistro(DocumentoRegistro $documentoRegistro): self
    {
        if (!$this->documentoRegistros->contains($documentoRegistro)) {
            $this->documentoRegistros[] = $documentoRegistro;
            $documentoRegistro->setArticulo($this);
        }

        return $this;
    }

    public function removeDocumentoRegistro(DocumentoRegistro $documentoRegistro): self
    {
        if ($this->documentoRegistros->removeElement($documentoRegistro)) {
            // set the owning side to null (unless already changed)
            if ($documentoRegistro->getArticulo() === $this) {
                $documentoRegistro->setArticulo(null);
            }
        }

        return $this;
    }

    public function getArticuloDescripcion(): ?ArticuloDescripcion
    {
        return $this->articuloDescripcion;
    }

    public function setArticuloDescripcion(ArticuloDescripcion $articuloDescripcion): self
    {
        $this->articuloDescripcion = $articuloDescripcion;

        // set the owning side of the relation if necessary
        if ($articuloDescripcion->getArticulo() !== $this) {
            $articuloDescripcion->setArticulo($this);
        }

        return $this;
    }

    public function getSubcategoria(): ?Subcategoria
    {
        return $this->subcategoria;
    }

    public function setSubcategoria(?Subcategoria $subcategoria): self
    {
        $this->subcategoria = $subcategoria;

        return $this;
    }

    /**
     * @return Collection|ArticuloTalla[]
     */
    public function getTallas(): Collection
    {
        return $this->tallas;
    }

    public function addTalla(ArticuloTalla $talla): self
    {
        if (!$this->tallas->contains($talla)) {
            $this->tallas[] = $talla;
            $talla->setArticulo($this);
        }

        return $this;
    }

    public function removeTalla(ArticuloTalla $talla): self
    {
        if ($this->tallas->removeElement($talla)) {
            // set the owning side to null (unless already changed)
            if ($talla->getArticulo() === $this) {
                $talla->setArticulo(null);
            }
        }

        return $this;
    }

    public function getSobrePedido(): ?bool
    {
        return $this->sobrePedido;
    }

    public function setSobrePedido(bool $sobrePedido): self
    {
        $this->sobrePedido = $sobrePedido;

        return $this;
    }
}
