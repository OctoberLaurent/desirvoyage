$(document).ready(function(){
    $( "#register_address" ).on( "keyup", function() {
        clearTimeout($.data(this, 'timer'));
        var wait = setTimeout(search, 400);
        $(this).data('timer', wait);    
    });
    $('input.autocomplete').autocomplete({
        onAutocomplete: loadData
    });

});

var results = {};
function search(){
    var autocomplete = document.getElementById("register_address");
    
    
    let query = autocomplete.value;
    //requete limit one
    let limit = "1";

    //console.log(query);
    //first request with an Ajax for the address
    $.ajax({
        method: "GET",
        url: "/api/address",
        data: { q: query }
      })
        .done(function( encoded ) {    
            
            let autodata = {};
            results = JSON.parse( encoded );
            for( let i in results.features ){
                let row = results.features[ i ];
                
                autodata[ row.properties.label ] = null;
            }

            $('#register_address').autocomplete("updateData", autodata);
            $('#register_address').autocomplete("open");

        });

}

function loadData( string ){
    /* On récupere les éléments id de l'adresse code postale et city */
    var address = document.getElementById("register_address");
    var Postcode = document.getElementById("register_postalCode");
    var city = document.getElementById("register_city");
   /* On boucle sur les result */
    for( var i in results.features ){
        var row = results.features[ i ];
        /* si le label est egal à la variable string alors on affiche code postale, rue et ville*/ 
        if( row.properties.label == string ){
            Postcode.value = row.properties.postcode;
            address.value = row.properties.name;
            city.value = row.properties.city;
        }
    }      
}
   