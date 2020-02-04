$(document).ready(function(){
    M.updateTextFields();
    $( "#register_address" ).on( "keyup", function() {
        clearTimeout($.data(this, 'timer'));
        var wait = setTimeout(search, 200);
        $(this).data('timer', wait);    
    });
    $('input.autocomplete').autocomplete({
        onAutocomplete: loadData
    });

});

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
        .done(function( results ) {    
            
            let autodata = {};
            let autopost = {};
            results = JSON.parse( results );
            
            
            for( let i in results.features ){
                let row = results.features[ i ];
                
                autodata[ row.properties.label ] = null;
                 autopost[ row.properties.postcode] = null;
            }

           
            //console.log( autodata );
            $('#register_address').autocomplete("updateData", autodata);
            $('#register_address').autocomplete("open");

        });

}

function loadData(){
    let query = document.getElementById("register_address").value;

    //second resquest with an ajax for postal code
    $.ajax({
        method: "GET",
        url: "/api/postal",
        data: { q: query, limit: 1 }
    })
    .done(function( results_post ) { 
        results_post = JSON.parse(results_post);
        var Postcode = document.getElementById("register_postalCode");
        var address = document.getElementById("register_address");
        var city = document.getElementById("register_city");
        Postcode.value = results_post.features[0].properties.postcode;
        address.value = results_post.features[0].properties.name;
        city.value = results_post.features[0].properties.city;

    });

 }