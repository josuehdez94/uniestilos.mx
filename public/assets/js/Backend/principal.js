function showFlotante(mensaje){
    $('.flotante').removeClass('d-none');
    $('.flotante span').text(mensaje);
}

function hideFlotante(){
    $('.flotante').addClass('d-none');
    $('.flotante span').text('');
}