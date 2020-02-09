$( document ).ready(function() {


$('.carousel').carousel({
    fullWidth: true,
    indicators: true,
});

// function for start caroussel
function autoplay() {
    $('.carousel').carousel('next');
    setTimeout(autoplay, 4500);
}

autoplay();

// for responsive sidebar
$('.sidenav').sidenav();

$('#flash').click(function(){
    $('#flash').remove()
})

    }); 