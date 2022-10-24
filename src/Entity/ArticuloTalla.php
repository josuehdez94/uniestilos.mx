<?php

namespace App\Entity;

use App\Repository\ArticuloTallaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ArticuloTallaRepository::class)
 */
class ArticuloTalla
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Articulo::class, inversedBy="tallas")
     * @ORM\JoinColumn(nullable=false)
     */
    private $articulo;

    /**
     * @ORM\ManyToOne(targetEntity=Almacen::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $almacen;

    /**
     * @ORM\ManyToOne(targetEntity=Tallas::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $talla;

    /**
     * @ORM\Column(type="integer")
     */
    private $existencia = 0;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $activa = false;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantidadPedida;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $cantidadApartada;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getArticulo(): ?Articulo
    {
        return $this->articulo;
    }

    public function setArticulo(?Articulo $articulo): self
    {
        $this->articulo = $articulo;

        return $this;
    }

    public function getAlmacen(): ?Almacen
    {
        return $this->almacen;
    }

    public function setAlmacen(?Almacen $almacen): self
    {
        $this->almacen = $almacen;

        return $this;
    }

    public function getTalla(): ?Tallas
    {
        return $this->talla;
    }

    public function setTalla(?Tallas $talla): self
    {
        $this->talla = $talla;

        return $this;
    }

    public function getExistencia(): ?int
    {
        return $this->existencia;
    }

    public function setExistencia(int $existencia): self
    {
        $this->existencia = $existencia;

        return $this;
    }

    public function getActiva(): ?bool
    {
        return $this->activa;
    }

    public function setActiva(?bool $activa): self
    {
        $this->activa = $activa;

        return $this;
    }

    public function getCantidadPedida(): ?int
    {
        return $this->cantidadPedida;
    }

    public function setCantidadPedida(?int $cantidadPedida): self
    {
        $this->cantidadPedida = $cantidadPedida;

        return $this;
    }

    public function getCantidadApartada(): ?int
    {
        return $this->cantidadApartada;
    }

    public function setCantidadApartada(?int $cantidadApartada): self
    {
        $this->cantidadApartada = $cantidadApartada;

        return $this;
    }
}
