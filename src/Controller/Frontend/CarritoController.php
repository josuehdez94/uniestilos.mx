<?php

namespace App\Controller\Frontend;

use App\Controller\Backend\ArticuloTallaController;
use App\Entity\Documento;
use App\Entity\DocumentoPago;
use App\Entity\DocumentoRegistro;
use App\Entity\User;
use App\Form\Frontend\Carrito\AgregarArticuloCarritoType;
use App\Form\Frontend\Carrito\AgregarArticuloSobrePedidoCarritoType;
use App\Form\Frontend\Carrito\SeleccionarDireccionType;
use App\Generales\Funciones;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Class CarritoController
 * @Route("/mi-cuenta/carrito")
 */
class CarritoController extends AbstractController
{
    const Token = 'TEST-723413152291982-102921-c6c27b339ebb97114cb527fc63436a3d-264821330';
    const LlavePublica = 'TEST-6e6a158c-04bb-4fdd-922b-5c5dfd7118bd';

    /**
     * @Route("/", name="front_carrito_index", methods={"GET"})
     */
    public function indexCarrito(Request $request, EntityManagerInterface $entityManager)
    {
        $usuario = $this->validaUsuario($request);
        if($usuario == 'hay sesion'){
            $documento = $entityManager->getRepository(\App\Entity\Documento::class)->getLastDocumentoByUser($this->getUser());
            $carrito = $entityManager->getRepository(\App\Entity\Documento::class)->findOneById($documento[1]);
            if(!$carrito){
                $carrito = null;
                $totalArticulos = 0;
            }else{
                $totalArticulos = $entityManager->getRepository(\App\Entity\Documento::class)->getTotalArticulosByDocumento($carrito->getId())['cantidad'];
            }
        }else{
            $documento = $entityManager->getRepository(\App\Entity\Documento::class)->getLastDocumentoByUserAnon($usuario);
            $carrito = null;
            $totalArticulos = 0;
        }

        return $this->render('Frontend/Carrito/carrito.html.twig', [
            'carrito' => $carrito,
            'totalArticulos' => $totalArticulos
        ]);
    }

    /**
     * @Route("/agregar-articulo/{user_uid}-{crypt}-{articulo_id}", name="front_carrito_agregar_articulo", requirements={"crypt": ".+"}, methods={"GET", "POST"})
     */
    public function agregarArticulo(Request $request, EntityManagerInterface $entityManager, $user_uid, $crypt, $articulo_id): Response
    {
        $cliente = $entityManager->getRepository(User::class)->findOneByUid($user_uid);
        if(!$cliente){
            throw $this->createNotFoundException('No se encontro el usuario.');
        }
        if($cliente->getId() != $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        #desencriptar
        $key = explode(',', $crypt)[1];
        $cadena = explode(',', $crypt)[0];
        $decrypt = new Funciones();
        $desencriptar = $decrypt->desencriptar($cadena, $key);
        if ($desencriptar != $cliente->getDecrypt()){
            throw $this->createNotFoundException('No puedes acceder a esta información.');
        }

        $articulo = $entityManager->getRepository(\App\Entity\Articulo::class)->findOneById($articulo_id);
        $usuario = $this->validaUsuario($request);
        $registro = new \App\Entity\DocumentoRegistro();
        $articulo->getSobrePedido() == true ? $formulario = AgregarArticuloSobrePedidoCarritoType::class : $formulario = AgregarArticuloCarritoType::class; 
        $form = $this->createForm($formulario, $registro, ['articulo' => $articulo]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $entityManager->getConnection()->beginTransaction();
            try {
                $documento = $entityManager->getRepository(\App\Entity\Documento::class)->getLastDocumentoByUser($this->getUser());
                if(!empty($documento[1])){
                    $carrito = $entityManager->getRepository(\App\Entity\Documento::class)->findOneById($documento[1]);
                }else{
                    #crear carrito
                    $ultimoId = $entityManager->getRepository(\App\Entity\Documento::class)->getLastDocumentoId();
                    $folio = $ultimoId[1] + 1;
                    $carrito = new \App\Entity\Documento();
                    $usuario == 'hay sesion' ? $carrito->setCliente($this->getUser()) : $carrito->setUserCookie($usuario);
                    $carrito->setTipo('R');
                    $carrito->setFolio('TO-' . $folio);
                    $entityManager->persist($carrito);
                    $entityManager->flush();
                }
                if($carrito->getFinalizado() == true){
                    return new Response(json_encode([
                        'type' => 'error',
                        'message' => 'Este carrito esta finalizado.'
                    ]));
                }
                if($carrito->getCancelado() == true){
                    return new Response(json_encode([
                        'type' => 'error',
                        'message' => 'Este carrito esta cancelado.'
                    ]));
                }
                $articuloTalla = $entityManager->getRepository(\App\Entity\ArticuloTalla::class)->findOneBy(['id' => $form->get('articuloTalla')->getData()->getId()]);
                $articuloRepetido = $entityManager->getRepository(\App\Entity\DocumentoRegistro::class)->findOneBy(
                    ['articulo' => $articulo->getId(), 'Documento' => $carrito->getId(), 'articuloTalla' => $articuloTalla->getId()]
                );
                $cantidad = $form->get('cantidad')->getData();
                if (empty($articuloRepetido)) {
                    if($articulo->getSobrePedido() == false){
                        $cantidad > $articuloTalla->getExistencia() ? $cantidadCarrito = $articuloTalla->getExistencia() : $cantidadCarrito = $cantidad;
                    }else{
                        $cantidad > 5 ? $cantidadCarrito = 5 : $cantidadCarrito = $cantidad;
                    }
                    #crear registro
                    $registro = new \App\Entity\DocumentoRegistro();
                    $registro->setArticulo($articulo);
                    $registro->setDocumento($carrito);
                    $registro->setArticuloTalla($articuloTalla);
                    $registro->setCantidad($cantidadCarrito);
                    $registro->setPrecio($form->get('precio')->getData());
                    $registro->setTotal(number_format($cantidadCarrito * $form->get('precio')->getData(), 2, '.', ''));
                    $entityManager->persist($registro);
                } else {
                    if($articulo->getSobrePedido() == false){
                        $articuloRepetido->getCantidad() + $cantidad > $articuloTalla->getExistencia() ? $cantidadCarrito = $articuloTalla->getExistencia() : $cantidadCarrito = $articuloRepetido->getCantidad() + $cantidad;
                    }else{
                        $articuloRepetido->getCantidad() + $cantidad > 5 ? $cantidadCarrito = 5 : $cantidadCarrito = $articuloRepetido->getCantidad() + $cantidad;
                    }
                    $total = ($cantidadCarrito) * ($form->get('precio')->getData());
                    $registro->setPrecio($form->get('precio')->getData());
                    $articuloRepetido->setCantidad($cantidadCarrito);
                    $articuloRepetido->setTotal(number_format($total,2, '.', ''));
                }
                $entityManager->flush();
                #comit de todos los flush
                $entityManager->getConnection()->commit();
            }catch (\Exception $e) {
                #rollback de todos los flush
                $entityManager->getConnection()->rollBack();
                return new Response(json_encode([
                    'type' => 'error',
                    'message' => $e->getMessage()
                ]));
            }
            return new Response(json_encode([
                'type' => 'success',
                'message' => 'articulo agregado al carrito'
            ]));
        }
        return new Response(json_encode([
            'type' => 'load',
            'message' => 'formulario cargado exitosamente',
            'content' => $this->renderView('Frontend/Carrito/agregarArticulo.html.twig', [
                'articulo' => $articulo,
                'form' => $form->createView()
            ])
            ]
        ));
    }

    /**
     * @Route("/articulo-agregado/{articulo_urlAmigable}", name="front_carrito_articulo_agregado", methods={"GET", "POST"})
     */
    public function articuloAgregado(Request $request, $articulo_urlAmigable)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $articulo = $entityManager->getRepository(\App\Entity\Articulo::class)->findOneByUrlAmigable($articulo_urlAmigable);
        if(!$articulo){
            throw $this->createNotFoundException('El articulo no ha sido encontrado');
        }
        $usuario = $this->validaUsuario($request);
        if($usuario == 'hay sesion'){
            $documento = $entityManager->getRepository(\App\Entity\Documento::class)->getLastDocumentoByUser($this->getUser());
        }else{
            $documento = $entityManager->getRepository(\App\Entity\Documento::class)->getLastDocumentoByUserAnon($usuario);

        }
        $carrito = $entityManager->getRepository(\App\Entity\Documento::class)->findOneById($documento[1]);
        if($carrito->getFinalizado() == true){
            throw $this->createNotFoundException('Este carrito esta finalizado.');
        }
        if($carrito->getCancelado() == true){
            throw $this->createNotFoundException('Este carrito esta cancelado.');
        }
        #calcular totales de carrito
        $totales = $this->calcularTotales($entityManager, $carrito);
        return $this->render('Frontend/Carrito/articuloAgregado.html.twig', [
            'articulo' => $articulo,
            'cantidad' => $totales['cantidadArticulos']
        ]);
    }

    /**
     * @Route("/actualizar-cantidad/{user_uid}-{crypt}-{documento_registro_id}", name="front_carrito_actualizar_cantidad", requirements={"crypt": ".+"}, methods={"GET"})
     */
    public function actualizarCantidad(Request $request, EntityManagerInterface $entityManager, $user_uid, $crypt, $documento_registro_id)
    {
        $cliente = $entityManager->getRepository(User::class)->findOneBy(['uid' => $user_uid]);
        if(!$cliente){
            return new Response(json_encode([
                'type' => 'error',
                'message' => 'No se encontro el usuario.'
            ]));
        }
        if($cliente->getId() != $this->getUser()->getId()){
            return new Response(json_encode([
                'type' => 'error',
                'message' => 'No puedes acceder a la información de otros clientes.'
            ]));
        }
        #desencriptar
        $key = explode(',', $crypt)[1];
        $cadena = explode(',', $crypt)[0];
        $decrypt = new Funciones();
        $desencriptar = $decrypt->desencriptar($cadena, $key);
        if ($desencriptar != $cliente->getDecrypt()){
            return new Response(json_encode([
                'type' => 'error',
                'message' => 'No puedes acceder a esta información.'
            ]));
        }
        $registro = $entityManager->getRepository(DocumentoRegistro::class)->findOneBy(['id' => $documento_registro_id]);
        if(!$registro){
            return new Response(json_encode([
                'type' => 'error',
                'message' => 'Articulo no encontrado en carrito'
            ]));
        }
        if($registro->getDocumento()->getCliente()->getId() != $this->getUser()->getId()){
            return new Response(json_encode([
                'type' => 'error',
                'message' => 'Este articulo no esta en tu carrito, no puedes eliminarlo'
            ]));
        }
        if($registro->getDocumento()->getFinalizado() == true){
            throw $this->createNotFoundException('Este carrito esta finalizado.');
        }
        if($registro->getDocumento()->getCancelado() == true){
            throw $this->createNotFoundException('Este carrito esta cancelado.');
        }
        $cantidad = $request->get('cantidad');
        $registro->setCantidad($cantidad);
        $registro->setTotal($registro->getPrecio() * $cantidad);
        $entityManager->flush();
        #calcular totales de carrito
        $totales = $this->calcularTotales($entityManager, $registro->getDocumento());
        return new Response(json_encode([
            'type' => 'success',
            'message' => 'articulo actualizado correctamente',
            'total' => $totales['total'],
            'envio' => $totales['envio'],
            'totalConEnvio' => $totales['totalConEnvio'],
            'totalRegistro' => number_format($registro->getTotal(), 2),
            'cantidadArticulos' => $totales['cantidadArticulos']
        ]));
        
    }

    /**
     * @Route("/eliminar-articulo/{user_uid}-{crypt}-{documento_registro_id}", name="front_carrito_eliminar_articulo", requirements={"crypt": ".+"}, methods={"POST"})
     */
    public function eliminarArticulo(EntityManagerInterface $entityManager, $user_uid, $crypt, $documento_registro_id)
    {
        $cliente = $entityManager->getRepository(User::class)->findOneBy(['uid' => $user_uid]);
        if(!$cliente){
            return new Response(json_encode([
                'type' => 'error',
                'message' => 'No se encontro el usuario.'
            ]));
        }
        if($cliente->getId() != $this->getUser()->getId()){
            return new Response(json_encode([
                'type' => 'error',
                'message' => 'No puedes acceder a la información de otros clientes.'
            ]));
        }
        #desencriptar
        $key = explode(',', $crypt)[1];
        $cadena = explode(',', $crypt)[0];
        $decrypt = new Funciones();
        $desencriptar = $decrypt->desencriptar($cadena, $key);
        if ($desencriptar != $cliente->getDecrypt()){
            return new Response(json_encode([
                'type' => 'error',
                'message' => 'No puedes acceder a esta información.'
            ]));
        }
        $registro = $entityManager->getRepository(DocumentoRegistro::class)->findOneBy(['id' => $documento_registro_id]);
        if(!$registro){
            return new Response(json_encode([
                'type' => 'error',
                'message' => 'Articulo no encontrado en carrito'
            ]));
        }
        if($registro->getDocumento()->getCliente()->getId() != $this->getUser()->getId()){
            return new Response(json_encode([
                'type' => 'error',
                'message' => 'Este articulo no esta en tu carrito, no puedes eliminarlo'
            ]));
        }
        if($registro->getDocumento()->getFinalizado() == true){
            throw $this->createNotFoundException('Este carrito esta finalizado.');
        }
        if($registro->getDocumento()->getCancelado() == true){
            throw $this->createNotFoundException('Este carrito esta cancelado.');
        }
        $entityManager->remove($registro);
        $entityManager->flush();
        #calcular totales de carrito
        $totales = $this->calcularTotales($entityManager, $registro->getDocumento());
        return new Response(json_encode([
            'type' => 'success',
            'message' => 'articulo eliminado del carrito',
            'total' => $totales['total'],
            'envio' => $totales['envio'],
            'totalConEnvio' => $totales['totalConEnvio']
        ]));

    }

    ###############################################################################################################################################################
    ############################# FUNCIONES PARA FINALIZAR CARRITO ###############################################################################################

    /**
     * @Route("/seleccionar-direccion/{user_uid}-{crypt}", name="front_carrito_seleccionar_direccion", requirements={"crypt": ".+"}, methods={"GET", "POST"})
     */
    public function seleccionarDireccion(EntityManagerInterface $entityManager, Request $request, $user_uid, $crypt)
    {
        $cliente = $entityManager->getRepository(User::class)->findOneBy(['uid' => $user_uid]);
        if(!$cliente){
            throw $this->createNotFoundException('Cliente no encontrado');
        }
        if($cliente->getId() != $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        #desencriptar
        $key = explode(',', $crypt)[1];
        $cadena = explode(',', $crypt)[0];
        $decrypt = new Funciones();
        $desencriptar = $decrypt->desencriptar($cadena, $key);
        if ($desencriptar != $cliente->getDecrypt()){
            throw $this->createNotFoundException('No puedes acceder a esta información.');
        }
        $usuario = $this->validaUsuario($request);
        if($usuario == 'hay sesion'){
            $documento = $entityManager->getRepository(\App\Entity\Documento::class)->getLastDocumentoByUser($this->getUser());
        }else{
            throw $this->createAccessDeniedException('Inicia sesión para continuar');
        }
        $carrito = $entityManager->getRepository(\App\Entity\Documento::class)->findOneById($documento[1]);
        if(!$carrito){
            throw $this->createNotFoundException('Carrito no encontrado.');
        }
        if($carrito->getCliente()->getId() !== $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        if($carrito->getFinalizado() == true){
            throw $this->createNotFoundException('Este carrito esta finalizado.');
        }
        if($carrito->getCancelado() == true){
            throw $this->createNotFoundException('Este carrito esta cancelado.');
        }
        $form = $this->createForm(SeleccionarDireccionType::class, $carrito, ['documento' => $carrito]);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            return new Response(json_encode([
                'type' => 'success',
                'message' => 'direccion seleccionada correctamente'
            ]));
        }
        return $this->render('Frontend/Carrito/seleccionarDireccion.html.twig', [
            'carrito' => $carrito,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/metodo-pago/{user_uid}-{crypt}", name="front_carrito_metodo_pago", requirements={"crypt": ".+"}, methods={"GET"})
     */
    public function metodoPago(EntityManagerInterface $entityManager, Request $request, $user_uid, $crypt)
    {
        $cliente = $entityManager->getRepository(User::class)->findOneBy(['uid' => $user_uid]);
        if(!$cliente){
            throw $this->createNotFoundException('Cliente no encontrado');
        }
        if($cliente->getId() != $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        #desencriptar
        $key = explode(',', $crypt)[1];
        $cadena = explode(',', $crypt)[0];
        $decrypt = new Funciones();
        $desencriptar = $decrypt->desencriptar($cadena, $key);
        if ($desencriptar != $cliente->getDecrypt()){
            throw $this->createNotFoundException('No puedes acceder a esta información.');
        }
        $usuario = $this->validaUsuario($request);
        if($usuario == 'hay sesion'){
            $documento = $entityManager->getRepository(\App\Entity\Documento::class)->getLastDocumentoByUser($this->getUser());
        }else{
            throw $this->createAccessDeniedException('Inicia sesión para continuar');
        }
        $carrito = $entityManager->getRepository(\App\Entity\Documento::class)->findOneById($documento[1]);
        if(!$carrito){
            throw $this->createNotFoundException('Carrito no encontrado.');
        }
        if($carrito->getCliente()->getId() !== $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        if($carrito->getFinalizado() == true){
            throw $this->createNotFoundException('Este carrito esta finalizado.');
        }
        if($carrito->getCancelado() == true){
            throw $this->createNotFoundException('Este carrito esta cancelado.');
        }
        if(empty($carrito->getClienteDireccion())){
            return $this->redirectToRoute('front_carrito_seleccionar_direccion',[
                'user_uid' => $cliente->getUid(),
                'crypt' => $cliente->getCrypt()
            ]);
        }
        $sdk = new \MercadoPago\SDK();
        $sdk::setAccessToken(self::Token);
        //$token = $sdk::setAccessToken('APP_USR-723413152291982-102921-7c73bc5fe253a3ba102322fe0ce25a67-264821330');
        $publicKey = self::LlavePublica;
        //$publicKey = 'APP_USR-8a45cc07-a297-4766-b04d-549efc242adf';
        #Crea un objeto de preferencia
        $preference = new \MercadoPago\Preference();
        $urlSuccess = $this->generateUrl('front_carrito_pago_aprobado', ['user_uid' => $cliente->getUid(), 'crypt' => $cliente->getCrypt()], UrlGeneratorInterface::ABSOLUTE_PATH);
        $urlFailure = $this->generateUrl('front_carrito_metodo_pago', ['user_uid' => $cliente->getUid(), 'crypt' => $cliente->getCrypt()], UrlGeneratorInterface::ABSOLUTE_PATH);
        if($_SERVER['SERVER_NAME'] == 'todopartes.mx' || $_SERVER['SERVER_NAME'] == 'www.todopartes.mx'  || $_SERVER['SERVER_NAME'] == 'dev.todopartes.mx' || $_SERVER['SERVER_NAME'] == 'beta.todopartes.mx' ){
            $urlOk = 'https://'.$_SERVER['SERVER_NAME'].$urlSuccess;
            $urlFail = 'https://'.$_SERVER['SERVER_NAME'].$urlFailure;
        }else{
            $urlOk = "http://localhost:8000".$urlSuccess;
            $urlFail = "http://localhost:8000".$urlFailure;
        }
        $preference->back_urls = [
            "success" => $urlOk,
            "failure" => $urlFail,
            "pending" => $urlOk
        ];
        $preference->auto_return = "approved";
        $payer = new \MercadoPago\Payer();
        $payer->name = $carrito->getCliente()->nombreCompleto();
        $preference->payer =$payer;
        $array = [];
        foreach($carrito->getRegistros() as $registro){
            #Crea un ítem en la preferencia
            $numeroItem = new \MercadoPago\Item();
            $numeroItem->title = $registro->getArticulo()->getDescripcion();
            $numeroItem->quantity = $registro->getCantidad();
            $numeroItem->picture_url = $registro->getArticulo()->getFotografiaPrincipal()->getWebMiniThumbnailNombreArchivo();
            $numeroItem->description = $registro->getArticulo()->getDescripcion();
            $numeroItem->unit_price = number_format($registro->getPrecio(), 2, '.', '');
            $array [] = $numeroItem;
        }
        if($carrito->getEnvio() > 0){
            #Crea un ítem en la preferencia
            $numeroItem = new \MercadoPago\Item();
            $numeroItem->title = 'Envio';
            $numeroItem->description = 'Costo de envio';
            $numeroItem->quantity = 1;
            $numeroItem->unit_price = number_format($carrito->getEnvio(), 2, '.', '');
            $array [] = $numeroItem;
        }
        /* $numeroItem2 = new \MercadoPago\Item();
        $numeroItem2->title = 'Compra en Todopartes';
        $numeroItem2->quantity = 1;
        $numeroItem2->unit_price = number_format($carrito->getTotalConEnvio(), 2, '.', ''); */
        //$preference->items = [$numeroItem];
        $preference->items = $array;
        $preference->payment_methods = array(
            "excluded_payment_methods" => array(
              array("id" => "oxxo", "id" => "paycash", "id" => "serfin", "id" => "bancomer", "id" => "banamex")
            ),
            "excluded_payment_types" => array(
              array("id" => "ticket", "id" => "atm", "id" => "bank_transfer")
            ),
            "installments" => 12
          );
        $preference->save();
        dump($preference);
        return $this->render('Frontend/Carrito/metodoPago.html.twig', [
            'carrito' => $carrito,
            'preference' => $preference
        ]);
    }

    /**
     * @Route("/pago-aprobado/{user_uid}-{crypt}", name="front_carrito_pago_aprobado", requirements={"crypt": ".+"}, methods={"GET"})
     */
    public function pagoAprobado(EntityManagerInterface $entityManager, Request $request, $user_uid, $crypt)
    {
        /* var_dump($_SERVER['HTTP_REFERER']);
        if(!isset($_SERVER['HTTP_REFERER'])){
            throw $this->createAccessDeniedException("Este pago no es valido, si haces mal uso de tu cuenta podria ser suspendida");
        } */
        /* if($_SERVER['SERVER_NAME'] == 'todopartes.mx'){
            $prefijo = 'https://'.$_SERVER['SERVER_NAME'];
        }else{
            $prefijo = 'https://localhost';
        } */
        //$prefijo = $_SERVER['SERVER_NAME'];
        /* if(strpos($_SERVER['HTTP_REFERER'],'https://www.mercadopago.com.mx') === false ){
            throw $this->createAccessDeniedException("Este pago no es valido, si haces mal uso de tu cuenta podria ser suspendida");
        } */
        $cliente = $entityManager->getRepository(User::class)->findOneBy(['uid' => $user_uid]);
        if(!$cliente){
            throw $this->createNotFoundException('Cliente no encontrado');
        }
        if($cliente->getId() != $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        #desencriptar
        $key = explode(',', $crypt)[1];
        $cadena = explode(',', $crypt)[0];
        $decrypt = new Funciones();
        $desencriptar = $decrypt->desencriptar($cadena, $key);
        if ($desencriptar != $cliente->getDecrypt()){
            throw $this->createNotFoundException('No puedes acceder a esta información.');
        }
        $usuario = $this->validaUsuario($request);
        if($usuario == 'hay sesion'){
            $documento = $entityManager->getRepository(\App\Entity\Documento::class)->getLastDocumentoByUser($this->getUser());
        }else{
            throw $this->createAccessDeniedException('Inicia sesión para continuar');
        }
        $carrito = $entityManager->getRepository(\App\Entity\Documento::class)->findOneById($documento[1]);
        if(!$carrito){
            throw $this->createNotFoundException('Carrito no encontrado.');
        }
        if($carrito->getCliente()->getId() !== $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        if($carrito->getFinalizado() == true){
            throw $this->createNotFoundException('Este carrito esta finalizado.');
        }
        if($carrito->getCancelado() == true){
            throw $this->createNotFoundException('Este carrito esta cancelado.');
        }
        $payment_id = $request->get('payment_id');
        $status = $request->get('status');
        $payment_type = $request->get('payment_type');
        $entityManager->getConnection()->beginTransaction();
        try {
            #agregar pago
            $pago = new DocumentoPago();
            $pago->setDocumento($carrito);
            $pago->setUsuario($cliente);
            $pago->setMonto($carrito->getTotalConEnvio());
            $pago->setFormaPago($payment_type);
            $pago->setPaymentId($payment_id);
            $pago->setEstatus($status);
            $entityManager->persist($pago);
            #cambiar string de crypt del usuario
            $encryt = new Funciones();
            $cadena = md5(random_int(-20000, 50000).date('Y-m-d g:i:s'));
            $key = md5(random_int(-50000, 20000).date('Y-m-d g:i:s'));
            $cliente->setCrypt($encryt->encriptar($cadena, $key).','.$key);
            $cliente->setDecrypt($cadena);
            #descontar existencias
            ArticuloTallaController::descontarExistencias($entityManager, $carrito);
            #finalizar carrito
            $carrito->setFinalizado(true);
            $carrito->setFechaHoraVenta(new \DateTime());
            $entityManager->flush();
            #comit de todos los flush
            $entityManager->getConnection()->commit();
        }catch (\Exception $e) {
            $entityManager->getConnection()->rollBack();
            throw $this->createNotFoundException($e);
        }
        return $this->redirectToRoute('front_carrito_resumen_compra', [
            'user_uid' => $cliente->getUid(),
            'crypt' => $cliente->getCrypt(),
            'carrito_id' => $carrito->getId()
        ]);
    }

    /**
     * @Route("/resumen-compra/{user_uid}-{crypt}-{carrito_id}", name="front_carrito_resumen_compra", requirements={"crypt": ".+"}, methods={"GET"})
     */
    public function resumenCompra(EntityManagerInterface $entityManager, Request $request, $user_uid, $crypt, $carrito_id)
    {
        $cliente = $entityManager->getRepository(User::class)->findOneBy(['uid' => $user_uid]);
        if(!$cliente){
            throw $this->createNotFoundException('Cliente no encontrado');
        }
        if($cliente->getId() != $this->getUser()->getId()){
            throw $this->createNotFoundException('No puedes acceder a la información de otros clientes.');
        }
        #desencriptar
        $key = explode(',', $crypt)[1];
        $cadena = explode(',', $crypt)[0];
        $decrypt = new Funciones();
        $desencriptar = $decrypt->desencriptar($cadena, $key);
        if ($desencriptar != $cliente->getDecrypt()){
            throw $this->createNotFoundException('No puedes acceder a esta información.');
        }
        $usuario = $this->validaUsuario($request);
        if($usuario != 'hay sesion'){
            throw $this->createAccessDeniedException('Inicia sesión para continuar');
        }
        $carrito = $entityManager->getRepository(Documento::class)->findOneBy(['id' => $carrito_id]);
        if(!$carrito){
            throw $this->createNotFoundException('Carrito no encontrado');
        }
        if($carrito->getFinalizado() != true){
            throw $this->createNotFoundException('Carrito no esta finalizado');
        }
        $fechaActual = new \DateTime();
        $interval = $fechaActual->diff($carrito->getFechaHoraVenta());
        #validacion para no mostrar la pagina cuando ya paso una hora desde que el cliente hizo la compra del cliente
        if($interval->h > 0){
            throw $this->createNotFoundException('Pagina no encontrada');
        }
        return $this->render('Frontend/Carrito/resumenCompra.html.twig', [
            'carrito' => $carrito
        ]);
    }


    /**
     * funcion para validar sesion de usuario, si no tiene se crea cookie para poder generar carrito
     * @param request
     */
    public function validaUsuario($request){
        if(empty($this->getUser())){
            #se verifica si existe la cookie
            if (isset($request->cookies->all()['usertp'])) {
                $cookieSesion = $request->cookies->all()['usertp'];
                #se crea cookie de sesion
            }else{
                $hash = hash('md5', date('Y-m-d g:i:s'));
                $response = new Response();
                $time = time() + (31536000);
                $response->headers->setCookie(new Cookie('userAnon', $hash, $time));
                $response->sendHeaders();
                $cookieSesion = $response->headers->getCookies()[0]->getValue();
            }
            return $cookieSesion;

        }
        return 'hay sesion';
    }

    /**
     * funcion para calcular los totales de un carrito
     * @param $documento
     */
    public static function calcularTotales(EntityManagerInterface $entityManager, $documento){
        $total = 0;
        $articulos = 0;
        foreach ($documento->getRegistros() as $registro){
            $total += $registro->getTotal();
            $articulos += $registro->getCantidad();
        }
        if($total > 3000 ){
            $totalConEnvio = $total;
            $envio = 0;
        }elseif($total > 0){
            $totalConEnvio = $total + 109.00;
            $envio = 109.00;
        }else{
            $totalConEnvio = 0;
            $envio = 0;
        }
        #actualizar totales
        $documento->setTotal(number_format($total, 2, '.', ''));
        $documento->setTotalConEnvio(number_format($totalConEnvio, 2, '.', ''));
        $documento->setEnvio($envio);
        $entityManager->flush();

        return [
            'total' => number_format($total,2),
            'envio' => number_format($envio, 2),
            'totalConEnvio' => number_format($totalConEnvio, 2),
            'cantidadArticulos' => $articulos
        ];
    }
}
