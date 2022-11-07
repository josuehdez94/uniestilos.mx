<?php

namespace App\Entity;

use App\Repository\ArticuloFotografiaRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ArticuloFotografiaRepository::class)
 * @UniqueEntity("nombre_archivo")
 * @ORM\HasLifecycleCallbacks()
 */
class ArticuloFotografia
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(name="id", type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="fecha_hora_creacion" , type="datetime")
     *
     */
    private $fechaHoraCreacion;

    /**
     *
     * @ORM\Column(name="nombre_archivo", type="string", length=100, unique=true, nullable=true)
     */
    private $nombreArchivo;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="usuario_creo_id", referencedColumnName="id")
     * })
     */
    private $usuarioSubio;

    /**
     *  @var \Articulo
     *
     * @ORM\ManyToOne(targetEntity="Articulo", inversedBy="fotografias")
     * @ORM\JoinColumns({
     * @ORM\JoinColumn(name="articulo_id", referencedColumnName="id", nullable=false)
     * })
     *
     */
    private $articulo;

    public function getId(): ?int
    {
        return $this->id;
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
    public function getNombreArchivo()
    {
        return $this->nombreArchivo;
    }

    /**
     * @param mixed $nombreArchivo
     */
    public function setNombreArchivo($nombreArchivo): void
    {
        $this->nombreArchivo = $nombreArchivo;
    }

    /**
     * @return \User
     */
    public function getUsuarioSubio()
    {
        return $this->usuarioSubio;
    }

    /**
     * @param \User $usuarioSubio
     */
    public function setUsuarioSubio($usuarioSubio): void
    {
        $this->usuarioSubio = $usuarioSubio;
    }

    /**
     * @return \Articulo
     */
    public function getArticulo()
    {
        return $this->articulo;
    }

    /**
     * @param \Articulo $articulo
     */
    public function setArticulo($articulo): void
    {
        $this->articulo = $articulo;
    }

    ######################Funciones Fotografias #################################
    private function getServerUrl() {
        //return (isset($_SERVER["HTTPS"]) ? 'https://' : 'http://') . $_SERVER['SERVER_NAME'] . '/';
        return (isset($_SERVER["HTTPS"]) ?  'https://': 'http://') . $_SERVER['SERVER_NAME'] . '/';
    }
    /**
     * Regresa una imagen por defecto cuando no encuentra la original
     */
    public function getNoLogoImage() {
        //return 'http://' . $_SERVER['SERVER_NAME'] . '/images/noimage.png';
        return  $this->getServerUrl(). 'assets/img/noImage.png';
        //return $this->getServerUrl() . '/images/noimage.png';
    }

    public function getMiniThumbnailNombreArchivo() {
        return null === $this->nombreArchivo ? null : $this->getUploadRootDirNombreArchivo() . '/mini_thumbs/' . $this->nombreArchivo;
    }

    public function getWebMiniThumbnailNombreArchivo() {
        if (file_exists($this->getMiniThumbnailNombreArchivo())) {
            //return null === $this->nombreArchivo ? null : 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/' . $this->getUploadDirNombreArchivo() . '/mini_thumbs/' . $this->nombreArchivo;
            return null === $this->nombreArchivo ? null :  $this->getUploadDirNombreArchivo() . '/mini_thumbs/' . $this->nombreArchivo;
        } else {
            return $this->getNoLogoImage();
        }
    }

    public function getThumbnailNombreArchivo() {
        return null === $this->nombreArchivo ? null : $this->getUploadRootDirNombreArchivo() . '/thumbs/' . $this->nombreArchivo;
    }

    public function getWebThumbnailNombreArchivo() {
        if (file_exists($this->getThumbnailNombreArchivo())) {
            //return null === $this->nombreArchivo ? null : 'http://' . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . '/' . $this->getUploadDirNombreArchivo() . '/thumbs/' . $this->nombreArchivo;
            return null === $this->nombreArchivo ? null : $this->getUploadDirNombreArchivo() . '/thumbs/' . $this->nombreArchivo;
        } else {
            return $this->getNoLogoImage();
        }
    }

    public function getAbsoluteNombreArchivo() {
        return null === $this->nombreArchivo ? null : $this->getUploadRootDirNombreArchivo() . '/' . $this->nombreArchivo;
    }
    public function getAbsoluteNombreArchivoThumb() {
        return null === $this->nombreArchivo ? null : $this->getUploadRootDirNombreArchivo() . '/thumbs/' . $this->nombreArchivo;
    }
    public function getAbsoluteNombreArchivoMiniThumb() {
        return null === $this->nombreArchivo ? null : $this->getUploadRootDirNombreArchivo() . '/mini_thumbs/' . $this->nombreArchivo;
    }

    public function getWebNombreArchivo() {
        if (file_exists($this->getAbsoluteNombreArchivo())) {
            return null === $this->nombreArchivo ? null : $this->getServerUrl() . $this->getUploadDirNombreArchivo() . '/' . $this->nombreArchivo;
        } else {
            return $this->getNoLogoImage();
        }
        //return null === $this->nombreArchivo ? null : $this->getServerUrl() . $this->getUploadDirNombreArchivo() . '/' . $this->nombreArchivo;
    }

    protected function getUploadRootDirNombreArchivo() {
        // the absolute directory picture where uploaded
        // documents should be saved
        return '/var/www/tiendaonline/market/uniestilos.mx/public'.$this->getUploadDirNombreArchivo();
    }

    protected function getUploadDirNombreArchivo() {
        // get rid of the __DIR__ so it doesn't screw up
        // when displaying uploaded doc/image in the view.
        return '/assets/img/back/articulos';
    }

    /**
    * "funcion para eliminar las imagenes en las carpetas mini_thumb, thumb y original
    * de un articulo."
    *
    * @ORM\PostRemove()
    */
    public function eliminarFotografiaArticulo(){
        /* imagen original */
        $nombreArchivoOriginal = $this->getAbsoluteNombreArchivo();
        if(file_exists($nombreArchivoOriginal)){
            unlink($nombreArchivoOriginal);
        }
         /* imagen thumb */
        $nombreArchivoThumb = $this->getAbsoluteNombreArchivoThumb();
        if(file_exists($nombreArchivoThumb)){
            unlink($nombreArchivoThumb);
        }
         /* imagen mini_thumb */
         $nombreArchivoMiniThumb = $this->getAbsoluteNombreArchivoMiniThumb();
        if(file_exists($nombreArchivoMiniThumb)){
            unlink($nombreArchivoMiniThumb);
        }
    }
}
