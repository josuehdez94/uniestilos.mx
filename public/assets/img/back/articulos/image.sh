#!/bin/bash

cd /var/www/tiendaonline/market/project/public/asset/img/back/articulos
convert $1 -fuzz 10% -transparent White $2
