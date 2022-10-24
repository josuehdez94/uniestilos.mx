function showFlotante(mensaje){
    $('.information').removeClass('d-none');
    $('.information .alert').html(mensaje);
}

function hideFlotante(){
    $('.information').addClass('d-none');
    $('.flotante .alert span').text('');
}