$( document ).ready(function() {

$('.carousel').carousel({
    padding: 600
});

// function for start caroussel
function autoplay() {
    $('.carousel').carousel('next');
    setTimeout(autoplay, 4500);
}

autoplay();

// for resposive sidebar
$('.sidenav').sidenav();

$('#flash').click(function(){
    $('#flash').remove()
})


// search funtion in left panel
$(document).on('input', '#travel_search_maxprice', function() {
    $('#maxprice').html( '<label id="maxprice" for="travel_search_maxprice" class="required">Prix maximum '+$(this).val()+' €</label>');
});

var tomorrow = new Date();
tomorrow.setDate(new Date().getDate()+1);

$('.datepicker').datepicker(
    {format: 'yyyy-mm-dd',
    containerHidden: '#hidden-input-outlet',
    minDate: tomorrow,
    firstDay: 0,
    lang : "fr",
    i18n: {
    months: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
    monthsShort: ['Jan', 'Fév', 'Mar', 'Avr', 'Juin', 'Juil', 'Aou', 'Sep', 'Oct', 'Nov', 'Dec'],
    weekdays: ["Lundi","Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi", "Dimanche"],
    weekdaysShort: ["Lun","Mar", "Mer", "Jeu", "Ven", "Sam", "Dim"],
    weekdaysAbbrev: ["L","M", "M", "J", "V", "S", "D"],
    cancel:'Annuler',
    clear:'Effacer',
    done:'Enregitrer'}
});

$('.datepicker-date-display').remove();

    $(document).on('change', '#travel_search_enddate', function() {
    
        startdate = $('#travel_search_startdate').val();
        enddate = $(this).val();
        var d1 = new Date(startdate)
        var d2 = new Date(enddate);
      
        if (startdate == ''){ 
            alert('Vous devez d\'abort indique la date de départ' );
            this.value = '';
        };

        if (d1 > d2 ){
            alert('la valeur indiquée n\'est pas correcte !' );
            this.value = '';
        }

    });

}); 