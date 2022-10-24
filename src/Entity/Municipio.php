<?php

namespace App\Entity;

use App\Repository\MunicipioRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Table(name="Municipio")
 * @ORM\Entity(repositoryClass=MunicipioRepository::class)
 */
class Municipio
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="nombre", type="string", length=100)
     */
    private $nombre;

    /**
     * @ORM\ManyToOne(targetEntity=Estado::class, inversedBy="municipios")
     * @ORM\JoinColumn(name="estado_id", nullable=false)
     */
    private $estado;

    /**
     * @ORM\OneToMany(targetEntity=CodigoPostal::class, mappedBy="municipio", orphanRemoval=true)
     */
    private $codigosPostales;
    
    public function __construct(){
        //$this->estado= new ArrayCollection();
        $this->codigosPostales = new ArrayCollection();

    }
    
    public function __toString() {
        return $this->nombre;
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

    public function getEstado()
    {
        return $this->estado;
    }

    public function setEstado(?Estado $estado): self
    {
        $this->estado = $estado;

        return $this;
    }

    public function getClienteDireccion(): ?ClienteDireccion
    {
        return $this->clienteDireccion;
    }

    public function setClienteDireccion(?ClienteDireccion $clienteDireccion): self
    {
        $this->clienteDireccion = $clienteDireccion;

        return $this;
    }

    /**
     * @return Collection|CodigoPostal[]
     */
    public function getCodigosPostales(): Collection
    {
        return $this->codigosPostales;
    }

    public function addCodigosPostale(CodigoPostal $codigosPostale): self
    {
        if (!$this->codigosPostales->contains($codigosPostale)) {
            $this->codigosPostales[] = $codigosPostale;
            $codigosPostale->setMunicipio($this);
        }

        return $this;
    }

    public function removeCodigosPostale(CodigoPostal $codigosPostale): self
    {
        if ($this->codigosPostales->removeElement($codigosPostale)) {
            // set the owning side to null (unless already changed)
            if ($codigosPostale->getMunicipio() === $this) {
                $codigosPostale->setMunicipio(null);
            }
        }

        return $this;
    }
}
