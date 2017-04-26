$(document).ready(function() {

    $("#sidebar").mouseenter(function(event) {
        $(this).removeClass('navalone');
        $(this).css('width', '124px');
    })
    .mouseleave(function(event) {
        $(this).addClass('navalone');
        $(this).css('width', '44px');
    });

    $('.box-logros .vermas').on('click', function(){
        $(this).closest('.box-avatar').addClass('open');
    })

    $('.box-avatar .icon-arrow-up').on('click', function(event) {
        event.preventDefault();
        $('.box-avatar').removeClass('open')
    });

    $('.lnk-pass').on('click', function(event) {
        $('.editar-pass').toggle();
    });

});