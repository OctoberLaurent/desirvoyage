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
    /* We recover the elements id of the address postal code and city */
    var address = document.getElementById("register_address");
    var Postcode = document.getElementById("register_postalCode");
    var city = document.getElementById("register_city");
   /* We loop on the results */
    for( var i in results.features ){
        var row = results.features[ i ];
        /* if the label is equal to the variable string then we display postal code, street and city */ 
        if( row.properties.label == string ){
            Postcode.value = row.properties.postcode;
            address.value = row.properties.name;
            city.value = row.properties.city;
        }
    }      
}