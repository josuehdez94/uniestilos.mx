<?php
namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * @author ESOP <esop@live.com>
 * @version 1.0 4/3/2020
 * @todo Esta clase sirve para inyectar un filtro 'unescape' en twig y poder limpiar la url de codigo hexadecimales
 */
class UrlDecodeExtension extends AbstractExtension 
{
    public function getFilters()
    {
        return array(
            new TwigFilter('unescape', array($this, 'unescape')),
        );
    }

    public function unescape($value)
    {
        # esta funcion es la encargada de limpiar la url
        return rawurldecode($value);
    }
}