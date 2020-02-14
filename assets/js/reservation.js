$(document).ready(function(){

     $('input:checkbox').each(function() {
        
        totalPrice = extractNumbers($( this ).parent().text())
        
        if ($( this ).is(':checked')){
           changePrice(totalPrice)
        } 

     });
    

    $('input:checkbox').click(function() {

        totalPrice = extractNumbers($( this ).parent().text())
        
        if ($( this ).is(':checked')){
           changePrice(totalPrice)
        } else {
            changePrice(-totalPrice)
        } 

    });

    $('label').hover(function() {
 
        $( ".cache" ).toggle( "slow", function() {
                $('this').show( "fast" );
         });
        
    });

    function changePrice(amount){

        price = $('#price').text();
        price = extractNumbers(price);
        $('#price').text(price+amount);
    }

    function extractNumbers(str){ 
        return Number(str.replace(/[^\d]/g, "")) 
    }
})