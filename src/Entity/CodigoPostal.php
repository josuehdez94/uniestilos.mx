<?php

namespace App\Entity;

use App\Repository\CodigoPostalRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CodigoPostalRepository::class)
 */
class CodigoPostal
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=5)
     */
    private $codigo;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $colonia;

    /**
     * @ORM\ManyToOne(targetEntity=Municipio::class, inversedBy="codigosPostales")
     * @ORM\JoinColumn(nullable=false)
     */
    private $municipio;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tipoColonia;

    public function __toString()
    {
        return $this->colonia;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCodigo(): ?string
    {
        return $this->codigo;
    }

    public function setCodigo(string $codigo): self
    {
        $this->codigo = $codigo;

        return $this;
    }

    public function getColonia(): ?string
    {
        return $this->colonia;
    }

    public function setColonia(string $colonia): self
    {
        $this->colonia = $colonia;

        return $this;
    }

    public function getMunicipio(): ?Municipio
    {
        return $this->municipio;
    }

    public function setMunicipio(?Municipio $municipio): self
    {
        $this->municipio = $municipio;

        return $this;
    }

    public function getTipoColonia(): ?string
    {
        return $this->tipoColonia;
    }

    public function setTipoColonia(?string $tipoColonia): self
    {
        $this->tipoColonia = $tipoColonia;

        return $this;
    }
}
