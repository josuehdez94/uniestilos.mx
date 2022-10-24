<?php
namespace App\Generales;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Funciones{

    public function urlAmigable($str, $replace = array("'", "@"), $delimiter = '-') {
        setlocale(LC_ALL, 'es_MX.UTF8');
        if (!empty($replace)) {
            $str = str_replace((array) $replace, ' ', $str);
        }

        $clean = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $clean);
        $clean = strtolower(trim($clean, '-'));
        $clean = preg_replace("/[\/_|+ -]+/", $delimiter, $clean);

        return $clean;
    }

    public function encriptar($string, $key)
    {
        $result = '';
        for($i=0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = trim(chr(ord($char)+ord($keychar)), '/');
            $result.=$char;
        }
        return base64_encode($result);
    }

    public function desencriptar($string, $key){
        $result = '';
        $string = base64_decode($string);
        for($i=0; $i<strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key))-1, 1);
            $char = trim(chr(ord($char)-ord($keychar)),'/');
            $result.=$char;
        }
        return $result;
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

}