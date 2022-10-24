<?php

  //require_once 'vendor/autoload.php';
require_once '/var/www/tiendaonline/pruebaMercadoPago/vendor/autoload.php';
   
  
  MercadoPago\MercadoPagoSdk::initialize(); 
  $config = MercadoPago\MercadoPagoSdk::config(); 
   
  
?>