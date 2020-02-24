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

$('#flash').click(function(){
    $('#flash').remove()
})

$('#modal1').modal();
   

}); 