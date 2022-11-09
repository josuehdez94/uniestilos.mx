#!/bin/bash

cd /var/www/tiendaonline/market/uniestilos.mx/public/assets/img/back/articulos
convert $1 -fuzz 10% -transparent White $2
cd /var/www/tiendaonline/market/uniestilos.mx/public/assets/img/back/articulos/thumbs
convert $1 -fuzz 10% -transparent White $2
cd /var/www/tiendaonline/market/uniestilos.mx/public/assets/img/back/articulos/mini_thumbs
convert $1 -fuzz 10% -transparent White $2

