$( document ).ready(function() {

$('.carousel').carousel({
    padding: 600
});

// function for start caroussel
function autoplay() {
    $('.carousel').carousel('next');
    setTimeout(autoplay, 4500);
}

// for resposive sidebar
$('.sidenav').sidenav();

$('#flash').click(function(){
    $('#flash').remove()
})

autoplay();
});