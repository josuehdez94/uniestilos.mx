<?php

namespace App\Entity;

use App\Repository\HistorialBusquedaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=HistorialBusquedaRepository::class)
 */
class HistorialBusqueda
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $termino;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $numeroBusquedas;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fechaHoraUltimaBusqueda;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTermino(): ?string
    {
        return $this->termino;
    }

    public function setTermino(string $termino): self
    {
        $this->termino = $termino;

        return $this;
    }

    public function getNumeroBusquedas(): ?int
    {
        return $this->numeroBusquedas;
    }

    public function setNumeroBusquedas(?int $numeroBusquedas): self
    {
        $this->numeroBusquedas = $numeroBusquedas;

        return $this;
    }

    public function getFechaHoraUltimaBusqueda(): ?\DateTimeInterface
    {
        return $this->fechaHoraUltimaBusqueda;
    }

    public function setFechaHoraUltimaBusqueda(\DateTimeInterface $fechaHoraUltimaBusqueda): self
    {
        $this->fechaHoraUltimaBusqueda = $fechaHoraUltimaBusqueda;

        return $this;
    }
}
