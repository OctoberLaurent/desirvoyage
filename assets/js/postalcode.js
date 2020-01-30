$(document).ready(function(){
  

    $( "#target" ).on( "keyup", function() {
        clearTimeout($.data(this, 'timer'));
        var wait = setTimeout(search, 500);
        $(this).data('timer', wait);
        
    });
    $('input.autocomplete').autocomplete();

  });
  //var elems = document.getElementById("target").value;
  //var instance = M.Adresse.getInstance(elem);
function search(){
    var autocomplete = document.getElementById("target");
    


    let query = autocomplete.value;
    let limit = "1";



    //console.log(query);
    
    $.ajax({
        method: "GET",
        url: "/api/address",
        data: { q: query }
      })
        .done(function( results ) {    
            
            let autodata = {};
            let autopost = {};
            results = JSON.parse( results );
            //console.log(results.features[0].properties.postcode);  
            
            for( let i in results.features ){
                let row = results.features[ i ];
                 //console.log( row );
                autodata[ row.properties.label] = null;
                autopost[ row.properties.postcode] = null;
                

            }

           
         //console.log( autodata );
        $('#target').autocomplete("updateData", autodata);

        });


            
 $( '#target' ).change(function() {

 query = document.getElementById("target").value; 

 
 $.ajax({
     method: "GET",
     url: "/api/postal",
     data: { q: query, limit: limit }
   })


   .done(function( results_post ) { 
    results_post = JSON.parse(results_post);
    var Postcode = document.getElementById("code");
    var result_final = results_post.features[0].properties.postcode;
    Postcode.value = result_final;
   
    // console.log(results_post.features[0].properties.postcode);

})




 
 
     

   });


}
