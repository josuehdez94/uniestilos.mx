<?php

namespace App\Entity;

use App\Repository\TallasRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TallasRepository::class)
 * @UniqueEntity("nombre", message="Ya existe una talla con este nombre", groups={"backend_talla_nueva", "backend_talla_editar"})
 */
class Tallas
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=15, unique=true)
     * @Assert\NotBlank(
     *  message="Ingresa un nombre para la talla",
     *  groups={"backend_talla_nueva", "backend_talla_editar"}
     * )
     * @Assert\Length(
     *      min = 1,
     *      max = 15,
     *      minMessage = "EL nombre debe contener minimo {{ limit }} caracteres",
     *      maxMessage = "El nombre debe contener maximo {{ limit }} caracteres",
     *      groups={"backend_talla_nueva", "backend_talla_editar"}
     * )
     */
    private $nombre;

    /**
     * @ORM\OneToMany(targetEntity=ArticuloTalla::class, mappedBy="talla", orphanRemoval=true)
     */
    private $tallas;

    /**
     * @ORM\Column(type="boolean")
     */
    private $desactivada;

    public function __construct()
    {
        $this->tallas = new ArrayCollection();
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
     * Get the value of tallas
     */ 
    public function getTallas()
    {
        return $this->tallas;
    }

    public function isDesactivada(): ?bool
    {
        return $this->desactivada;
    }

    public function setDesactivada(bool $desactivada): self
    {
        $this->desactivada = $desactivada;

        return $this;
    }
}
