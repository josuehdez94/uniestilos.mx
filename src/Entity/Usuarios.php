<?php

namespace App\Entity;

use App\Repository\UsuariosRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Table(name="usuarios")
 * @ORM\Entity(repositoryClass=UsuariosRepository::class)
 */
class Usuarios
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="nombre" ,type="string", length=50)
     */
    private $nombre;

    /**
     * @ORM\Column(name="usuario", type="string", length=10)
     */
    private $usuario;

    /**
     * @ORM\Column(name="fecha_hora_creacion", type="datetime")
     */
    private $fechaHoraCreacion;

    /**
     * @ORM\Column(name="fecha_hora_edicion", type="datetime", nullable=true)
     */
    private $fechaHoraEdicion;

    /**
     * @ORM\Column(name="contrasena", type="string", length=8, nullable=true)
     */
    private $contrasena;

    /**
     * @ORM\Column(name="activo", type="boolean", nullable=true)
     */
    private $activo;

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

    public function getUsuario(): ?string
    {
        return $this->usuario;
    }

    public function setUsuario(string $usuario): self
    {
        $this->usuario = $usuario;

        return $this;
    }

    public function getFechaHoraCreacion(): ?\DateTimeInterface
    {
        return $this->fechaHoraCreacion;
    }

    public function setFechaHoraCreacion(\DateTimeInterface $fechaHoraCreacion): self
    {
        $this->fechaHoraCreacion = fechaHoraCreacion;

        return $this;
    }

    public function getFechaHoraEdicion(): ?\DateTimeInterface
    {
        return $this->fechaHoraEdicion;
    }

    public function setFechaHoraEdicion(?\DateTimeInterface $fechaHoraEdicion): self
    {
        $this->fechaHoraEdicion = fechaHoraEdicion;

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

    public function getContrasena()
    {
        return $this->contrasena;
    }

    public function setContrasena(string $contrasena):self
    {
        $this->contrasena = $contrasena;

        return $this;
    }
}
