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

autoplay();
});