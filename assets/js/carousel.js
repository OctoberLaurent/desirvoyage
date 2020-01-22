$( document ).ready(function() {

  $('.carousel').carousel({
    padding: 600
});

autoplay();
function autoplay() {
    $('.carousel').carousel('next');
    setTimeout(autoplay, 4500);
}

$('.sidenav').sidenav();

});