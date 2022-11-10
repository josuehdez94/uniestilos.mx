<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="User")
 * @UniqueEntity(
 *     fields={"email"},
 *     errorPath="email",
 *     message="Este email ya pertenece a una cuenta",
 *     groups={"backend_user_nuevo", "front_user_nuevo"}
 * )
 * @ORM\Entity(repositoryClass=UserRepository::class)
 */
class User implements UserInterface , PasswordAuthenticatedUserInterface{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="nombre", type="string", length=25)
     */
    private $nombre;

    /**
     * @ORM\Column(name="apellido_paterno", type="string", length=25)
     */
    private $apellidoPaterno;

    /**
     * @ORM\Column(name="apellido_materno", type="string", length=25)
     */
    private $apellidoMaterno;

    /**
     * @ORM\Column(name="email", type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(name="tipo_usuario", type="string", length=1)
     */
    private $tipoUser;

    /**
     * @ORM\Column(name="activo", type="boolean")
     */
    private $activo = 1;
    
    /**
     * @ORM\Column(name="cuenta_validada", type="boolean")
     */
    private $cuentaValidada = 0;

    /**
     * @ORM\Column(name="roles", type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(name="password", type="string")
     */
    private $password;
    
    /**
     * 
     * @ORM\Column(name="clave_verificacion", type="string")
     */
    private $claveVerificacion;

    /**
     *
     * @var type 
     */
    protected $oldPassword;

    /**
     * @ORM\Column(name="uid", type="string", length=100, nullable=false, unique=true)
     */
    private $uid;

    /**
     * @ORM\Column(name="crypt", type="string", length=255, nullable=true, unique=true)
     */
    private $crypt;

    /**
     * @ORM\Column(name="decrypt", type="string", length=255, nullable=true, unique=true)
     */
    private $decrypt;

    /**
     * @ORM\ManyToOne(targetEntity=ClienteDireccion::class)
     * @ORM\JoinColumn(name="direccion_principal_id", nullable=true)
     */
    private $direccionPrincipal;

    /**
     * @ORM\OneToMany(targetEntity=Documento::class, mappedBy="cliente")
     */
    private $documentos;

    /**
     * @ORM\OneToMany(targetEntity=ClienteDireccion::class, mappedBy="cliente")
     */
    private $clienteDirecciones;

    /**
     * @ORM\ManyToMany(targetEntity=RoleSistema::class, inversedBy="users")
     */
    private $rolesSistema;


    public function __construct()
    {
        $this->documentos = new ArrayCollection();
        $this->clienteDirecciones = new ArrayCollection();
        $this->rolesSistema = new ArrayCollection();
    }

    public function getId(): ?int {
        return $this->id;
    }

    public function getEmail(): ?string {
        return $this->email;
    }

    public function setEmail(string $email): self {
        $this->email = $email;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * @param mixed $nombre
     */
    public function setNombre($nombre): void {
        $this->nombre = $nombre;
    }

    /**
     * @return mixed
     */
    public function getApellidoPaterno() {
        return $this->apellidoPaterno;
    }

    /**
     * @param mixed $apellidoPaterno
     */
    public function setApellidoPaterno($apellidoPaterno): void {
        $this->apellidoPaterno = $apellidoPaterno;
    }

    /**
     * @return mixed
     */
    public function getApellidoMaterno() {
        return $this->apellidoMaterno;
    }

    /**
     * @param mixed $apellidoMaterno
     */
    public function setApellidoMaterno($apellidoMaterno): void {
        $this->apellidoMaterno = $apellidoMaterno;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array {
        $roles = [];
        foreach($this->rolesSistema as $rol){
            $roles [] = $rol->getRole();
        }
        // guarantee every user at least has ROLE_USER
        if($this->tipoUser == 'C'){
            $roles[] = 'ROLE_USER';
        }else if($this->tipoUser == 'E'){
            $roles[] = 'ROLE_USER_BACK';
        }

        return array_unique($roles);
    }

    public function setRoles(array $roles): self {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string {
        return (string) $this->password;
    }

    public function setPassword(string $password): self {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt() {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials() {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @see UserInterface
     */
    public function getOldPassword(): string {
        return (string) $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self {
        $this->oldPassword = $oldPassword;

        return $this;
    }
    
    /**
     * 
     */
    public function getTipoUser(): string {
        return $this->tipoUser;
    }

    /**
     * 
     */
    public function setTipoUser($tipoUser): self {
        $this->tipoUser = $tipoUser;

        return $this;
    }
    

    /**
     * 
     */
    public function setClaveVerificacion($claveVerificacion): self {
        $this->claveVerificacion = $claveVerificacion;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getClaveVerificacion() {
        return $this->claveVerificacion;
    }
    
    /**
     * 
     */
    public function getCuentaValidada(): string {
        return $this->cuentaValidada;
    }

    /**
     * 
     */
    public function setCuentaValidada($cuentaValidada): self {
        $this->cuentaValidada = $cuentaValidada;

        return $this;
    }

    /**
     * @param mixed activo
     */
    public function setActivo($activo): void {
        $this->activo = $activo;
    }
    
    /**
     * 
     */
    public function getActivo(): string {
        return $this->activo;
    }
    
    

    /**
     * funcion para retornar el nombre completo del usuario
     */
    public function nombreCompleto() {
        return ucwords($this->nombre . ' ' . $this->apellidoPaterno . ' ' . $this->apellidoMaterno);
    }

    /**
     * @return Collection|Documento[]
     */
    public function getDocumentos(): Collection
    {
        return $this->documentos;
    }

    public function addDocumento(Documento $documento): self
    {
        if (!$this->documentos->contains($documento)) {
            $this->documentos[] = $documento;
            $documento->setCliente($this);
        }

        return $this;
    }

    public function removeDocumento(Documento $documento): self
    {
        if ($this->documentos->removeElement($documento)) {
            // set the owning side to null (unless already changed)
            if ($documento->getCliente() === $this) {
                $documento->setCliente(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ClienteDireccion[]
     */
    public function getClienteDirecciones(): Collection
    {
        return $this->clienteDirecciones;
    }

    public function addClienteDireccione(ClienteDireccion $clienteDireccione): self
    {
        if (!$this->clienteDirecciones->contains($clienteDireccione)) {
            $this->clienteDirecciones[] = $clienteDireccione;
            $clienteDireccione->setCliente($this);
        }

        return $this;
    }

    public function removeClienteDireccione(ClienteDireccion $clienteDireccione): self
    {
        if ($this->clienteDirecciones->removeElement($clienteDireccione)) {
            // set the owning side to null (unless already changed)
            if ($clienteDireccione->getCliente() === $this) {
                $clienteDireccione->setCliente(null);
            }
        }

        return $this;
    }

    public function getDireccionPrincipal(): ?ClienteDireccion
    {
        return $this->direccionPrincipal;
    }

    public function setDireccionPrincipal(?ClienteDireccion $direccionPrincipal): self
    {
        $this->direccionPrincipal = $direccionPrincipal;

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
     * @return Collection|RoleSistema[]
     */
    public function getRolesSistema(): Collection
    {
        return $this->rolesSistema;
    }

    public function addRoleSistema(RoleSistema $roleSistema): self
    {
        if (!$this->rolesSistema->contains($roleSistema)) {
            $this->rolesSistema[] = $roleSistema;
        }

        return $this;
    }

    public function removeRoleSistema(RoleSistema $roleSistema)
    {
        $this->rolesSistema->removeElement($roleSistema);
        return $this;

    }

}
