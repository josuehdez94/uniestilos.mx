<?php

namespace App\Entity;

use App\Repository\ClienteDireccionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="cliente_direccion")
 * @ORM\Entity(repositoryClass=ClienteDireccionRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class ClienteDireccion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="nombre_completo", type="string", length=255)
     */
    private $nombreCompleto;

    /**
     * @ORM\Column(name="calle", type="string", length=100)
     */
    private $calle;

    /**
     * @ORM\Column(name="numero_exterior", type="integer")
     */
    private $numeroExterior;

    /**
     * @ORM\Column(name="numero_interior", type="integer", nullable=true)
     */
    private $numeroInterior;

    /**
     * @ORM\Column(name="telefono", type="bigint")
     */
    private $telefono;

    /**
     * @ORM\Column(name="instrucciones_entrega", type="string", length=255, nullable=true)
     */
    private $instrucionesEntrega;

    /**
     * @ORM\Column(name="referencias", type="string", length=255, nullable=true)
     */
    private $Referencias;

    /**
     * @ORM\Column(name="crypt", type="string", length=255, nullable=true, unique=true)
     */
    private $crypt;

    /**
     * @ORM\Column(name="decrypt", type="string", length=255, nullable=true, unique=true)
     */
    private $decrypt;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="clienteDirecciones")
     * @ORM\JoinColumn(name="cliente_id", nullable=false)
     */
    private $cliente;

    /**
     * @ORM\Column(name="eliminada", type="boolean")
     */
    private $eliminada = false;


    /**
     * @ORM\Column(name="uid", type="string", length=100, nullable=false, unique=true)
     */
    private $uid;

    /**
     * @ORM\Column(name="fecha_hora_creacion", type="datetime", nullable=true)
     */
    private $fechaHoraCreacion;

    /**
     * @ORM\Column(name="fecha_hora_edicion", type="datetime", nullable=true)
     */
    private $fechaHoraEdicion;

    /**
     * @ORM\Column(name="fecha_hora_eliminada", type="datetime", nullable=true)
     */
    private $fechaHoraEliminada;

    /**
     * @ORM\ManyToOne(targetEntity=CodigoPostal::class)
     */
    private $codigoPostalColonia;


    public function __toString()
    {
        //return $this->nombreCompleto;
        isset($this->numeroInterior) ? $numeroInterior = 'Int. '.$this->numeroInterior : $numeroInterior = '';

        $direccion = $this->nombreCompleto.' - '.ucwords($this->calle).' #'.$this->numeroExterior.' '.$numeroInterior.' '.ucwords($this->codigoPostalColonia->getColonia()).' '.$this->codigoPostalColonia->getMunicipio()->getEstado()->getNombre().','.$this->codigoPostalColonia->getMunicipio()->getNombre().' CP: '.$this->codigoPostalColonia->getCodigo();
        //$direccion = $this->calle;
        return $direccion;
    }
    public function __construct()
    {
        $this->fechaHoraCreacion = new \DateTime();
        $uid = md5(date('Y-m-d g:i:s'));
        $this->uid = $uid;
    }


    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function onUpdate()
    {
        if(empty($this->uid)){
            $uid = md5(date('Y-m-d g:i:s'));
            $this->uid = $uid;
        }
        $this->fechaHoraEdicion = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNombreCompleto()
    {
        return $this->nombreCompleto;
    }

    public function setNombreCompleto($nombreCompleto = null)
    {
        $this->nombreCompleto = $nombreCompleto;

        return $this;
    }

    public function getCalle()
    {
        return $this->calle;
    }

    public function setCalle($calle)
    {
        $this->calle = $calle;

        return $this;
    }

    public function getNumeroExterior()
    {
        return $this->numeroExterior;
    }

    public function setNumeroExterior($numeroExterior)
    {
        $this->numeroExterior = $numeroExterior;

        return $this;
    }

    public function getNumeroInterior()
    {
        return $this->numeroInterior;
    }

    public function setNumeroInterior($numeroInterior)
    {
        $this->numeroInterior = $numeroInterior;

        return $this;
    }

    public function getTelefono()
    {
        return $this->telefono;
    }

    public function setTelefono($telefono)
    {
        $this->telefono = $telefono;

        return $this;
    }

    public function getInstrucionesEntrega()
    {
        return $this->instrucionesEntrega;
    }

    public function setInstrucionesEntrega($instrucionesEntrega)
    {
        $this->instrucionesEntrega = $instrucionesEntrega;

        return $this;
    }

    public function getReferencias()
    {
        return $this->Referencias;
    }

    public function setReferencias($Referencias)
    {
        $this->Referencias = $Referencias;

        return $this;
    }

    public function getCliente(): ?User
    {
        return $this->cliente;
    }

    public function setCliente($cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    public function direccionCompleta()
    {

        isset($this->numeroInterior) ? $numeroInterior = 'Int. '.$this->numeroInterior : $numeroInterior = '';

        $direccion = ucwords($this->calle).' #'.$this->numeroExterior.' '.$numeroInterior.' '.ucwords($this->codigoPostalColonia->getColonia()).' '.$this->codigoPostalColonia->getMunicipio()->getEstado()->getNombre().','.$this->codigoPostalColonia->getMunicipio()->getNombre().' CP: '.$this->codigoPostalColonia->getCodigo();
        //$direccion = $this->calle;
        return $direccion;
    }

    /**
     * @return mixed
     */
    public function getCrypt()
    {
        return $this->crypt;
    }

    /**
     * @param mixed $crypt
     */
    public function setCrypt($crypt)
    {
        $this->crypt = $crypt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDecrypt()
    {
        return $this->decrypt;
    }

    /**
     * @param mixed $crypt
     */
    public function setDecrypt($decrypt)
    {
        $this->decrypt = $decrypt;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * @param mixed $uid
     */
    public function setUid($uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return mixed
     */
    public function getFechaHoraCreacion()
    {
        return $this->fechaHoraCreacion;
    }

    /**
     * @param mixed $fechaHoraCreacion
     */
    public function setFechaHoraCreacion($fechaHoraCreacion): void
    {
        $this->fechaHoraCreacion = $fechaHoraCreacion;
    }

    /**
     * @return mixed
     */
    public function getFechaHoraEdicion()
    {
        return $this->fechaHoraEdicion;
    }

    /**
     * @param mixed $fechaHoraEdicion
     */
    public function setFechaHoraEdicion($fechaHoraEdicion): void
    {
        $this->fechaHoraEdicion = $fechaHoraEdicion;
    }

    /**
     * @return bool
     */
    public function isEliminada(): bool
    {
        return $this->eliminada;
    }

    /**
     * @param bool $eliminada
     */
    public function setEliminada(bool $eliminada): void
    {
        $this->eliminada = $eliminada;
    }

    /**
     * @return mixed
     */
    public function getFechaHoraEliminada()
    {
        return $this->fechaHoraEliminada;
    }

    /**
     * @param mixed $fechaHoraEliminada
     */
    public function setFechaHoraEliminada($fechaHoraEliminada): void
    {
        $this->fechaHoraEliminada = $fechaHoraEliminada;
    }

    public function getCodigoPostalColonia(): ?CodigoPostal
    {
        return $this->codigoPostalColonia;
    }

    public function setCodigoPostalColonia(?CodigoPostal $codigoPostalColonia): self
    {
        $this->codigoPostalColonia = $codigoPostalColonia;

        return $this;
    }

}
