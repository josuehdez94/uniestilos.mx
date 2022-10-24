<?php

namespace App\Entity;

use App\Generales\Funciones;
use App\Repository\DocumentoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="Documento")
 * @ORM\Entity(repositoryClass=DocumentoRepository::class)
 */
class Documento
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="folio", type="string", length=10)
     */
    private $folio;

    /**
     * @ORM\Column(name="tipo", type="string", length=10)
     */
    private $tipo;

    /**
     * @ORM\Column(name="total", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $total;

    /**
     * @ORM\Column(name="total_con_envio", type="decimal", precision=10, scale=2, nullable=true)
     */
    private $totalConEnvio;

    /**
     * @ORM\Column(name="fecha_hora_creacion", type="datetime")
     */
    private $fechaHoraCreacion;

    /**
     * @ORM\Column(name="fecha_hora_pedido", type="datetime", nullable=true)
     */
    private $fechaHoraPedido;

    /**
     * @ORM\Column(name="fecha_hora_venta", type="datetime", nullable=true)
     */
    private $fechaHoraVenta;

    /**
     * @ORM\Column(name="crypt", type="string", length=255, nullable=true, unique=true)
     */
    private $crypt;

    /**
     * @ORM\Column(name="decrypt", type="string", length=255, nullable=true, unique=true)
     */
    private $decrypt;

    /**
     * @ORM\Column(name="user_cookie", type="string", length=50, nullable=true, unique=true)
     */
    private $userCookie;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="documentos")
     * @ORM\JoinColumn(name="cliente_id", nullable=true)
     */
    private $cliente;

    /**
     * @ORM\ManyToOne(targetEntity=\App\Entity\ClienteDireccion::class)
     * @ORM\JoinColumn(name="cliente_direccion_id", nullable=true)
     */
    private $clienteDireccion;

    /**
     * @ORM\OneToMany(targetEntity=DocumentoRegistro::class, mappedBy="Documento", orphanRemoval=true)
     */
    private $registros;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $envio;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=2, nullable=true)
     */
    private $descuento;

    /**
     * @ORM\OneToMany(targetEntity=DocumentoPago::class, mappedBy="documento", orphanRemoval=true)
     */
    private $pagos;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $finalizado = false;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $cancelado = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $pagado;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enviado;

    /**
     * @ORM\Column(type="boolean")
     */
    private $entregado;

    public function __construct()
    {
        $this->registros = new ArrayCollection();
        $this->fechaHoraCreacion = new \DateTime();
        $this->pagos = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function encrypt(){
        $encryt = new Funciones();
        $cadena = md5(random_int(-20000, 50000).date('Y-m-d g:i:s'));
        $key = md5(random_int(-50000, 20000).date('Y-m-d g:i:s'));
        $this->encrypt = $encryt->encriptar($cadena, $key).','.$key;
        $this->decrypt = $cadena;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFolio(): ?string
    {
        return $this->folio;
    }

    public function setFolio(string $folio): self
    {
        $this->folio = $folio;

        return $this;
    }

    public function getTipo(): ?string
    {
        return $this->tipo;
    }

    public function setTipo(string $tipo): self
    {
        $this->tipo = $tipo;

        return $this;
    }

    public function getTotal(): ?string
    {
        return $this->total;
    }

    public function setTotal(?string $total): self
    {
        $this->total = $total;

        return $this;
    }

    public function getTotalConEnvio(): ?string
    {
        return $this->totalConEnvio;
    }

    public function setTotalConEnvio(?string $totalConEnvio): self
    {
        $this->totalConEnvio = $totalConEnvio;

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

    public function getFechaHoraPedido(): ?\DateTimeInterface
    {
        return $this->fechaHoraPedido;
    }

    public function setFechaHoraPedido(?\DateTimeInterface $fechaHoraPedido): self
    {
        $this->fechaHoraPedido = $fechaHoraPedido;

        return $this;
    }

    public function getFechaHoraVenta(): ?\DateTimeInterface
    {
        return $this->fechaHoraVenta;
    }

    public function setFechaHoraVenta(?\DateTimeInterface $fechaHoraVenta): self
    {
        $this->fechaHoraVenta = $fechaHoraVenta;

        return $this;
    }

    public function getCliente(): ?User
    {
        return $this->cliente;
    }

    public function setCliente(?User $cliente): self
    {
        $this->cliente = $cliente;

        return $this;
    }

    /**
     * @return Collection|DocumentoRegistro[]
     */
    public function getRegistros(): Collection
    {
        return $this->registros;
    }

    public function addRegistro(DocumentoRegistro $registro): self
    {
        if (!$this->registros->contains($registro)) {
            $this->registros[] = $registro;
            $registro->setDocumento($this);
        }

        return $this;
    }

    public function removeRegistro(DocumentoRegistro $registro): self
    {
        if ($this->registros->removeElement($registro)) {
            // set the owning side to null (unless already changed)
            if ($registro->getDocumento() === $this) {
                $registro->setDocumento(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClienteDireccion()
    {
        return $this->clienteDireccion;
    }

    /**
     * @param mixed $clienteDireccion
     */
    public function setClienteDireccion($clienteDireccion): void
    {
        $this->clienteDireccion = $clienteDireccion;
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
    public function setCrypt($crypt): void
    {
        $this->crypt = $crypt;
    }

    /**
     * @return mixed
     */
    public function getDecrypt()
    {
        return $this->decrypt;
    }

    /**
     * @param mixed $decrypt
     */
    public function setDecrypt($decrypt): void
    {
        $this->decrypt = $decrypt;
    }

    /**
     * @return mixed
     */
    public function getUserCookie()
    {
        return $this->userCookie;
    }

    /**
     * @param mixed $userCookie
     */
    public function setUserCookie($userCookie): void
    {
        $this->userCookie = $userCookie;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function actualizarSeguridad()
    {
        $encryt = new Funciones();
        $cadena = md5(random_int(-10000000, 10000000).date('Y-m-d g:i:s'));
        $key = md5(random_int(-10000000, 10000000).date('Y-m-d g:i:s'));
        if(empty($this->fechaHoraCreacion)){
            $this->fechaHoraCreacion = new \DateTime();
        }
        $this->crypt = $encryt->encriptar($cadena, $key).','.$key;
        $this->decrypt = $cadena;
    }

    public function getEnvio(): ?string
    {
        return $this->envio;
    }

    public function setEnvio(?string $envio): self
    {
        $this->envio = $envio;

        return $this;
    }

    public function getDescuento(): ?string
    {
        return $this->descuento;
    }

    public function setDescuento(?string $descuento): self
    {
        $this->descuento = $descuento;

        return $this;
    }

    /**
     * @return Collection|DocumentoPago[]
     */
    public function getPagos(): Collection
    {
        return $this->pagos;
    }

    public function addPago(DocumentoPago $pago): self
    {
        if (!$this->pagos->contains($pago)) {
            $this->pagos[] = $pago;
            $pago->setDocumento($this);
        }

        return $this;
    }

    public function removePago(DocumentoPago $pago): self
    {
        if ($this->pagos->removeElement($pago)) {
            // set the owning side to null (unless already changed)
            if ($pago->getDocumento() === $this) {
                $pago->setDocumento(null);
            }
        }

        return $this;
    }

    public function getFinalizado(): ?bool
    {
        return $this->finalizado;
    }

    public function setFinalizado(?bool $finalizado): self
    {
        $this->finalizado = $finalizado;

        return $this;
    }

    public function getCancelado(): ?bool
    {
        return $this->cancelado;
    }

    public function setCancelado(?bool $cancelado): self
    {
        $this->cancelado = $cancelado;

        return $this;
    }

    public function getPagado(): ?bool
    {
        return $this->pagado;
    }

    public function setPagado(bool $pagado): self
    {
        $this->pagado = $pagado;

        return $this;
    }

    public function getEnviado(): ?bool
    {
        return $this->enviado;
    }

    public function setEnviado(bool $enviado): self
    {
        $this->enviado = $enviado;

        return $this;
    }

    public function getEntregado(): ?bool
    {
        return $this->entregado;
    }

    public function setEntregado(bool $entregado): self
    {
        $this->entregado = $entregado;

        return $this;
    }
}
