$(document).ready(function(){
    $( "#target" ).on( "keypress", function() {
        clearTimeout(this, 'timer');
  var wait = setTimeout(search, 500);
  $(this).data('timer', wait);
    });
    function search() {
        $.post("user.php", {nStr: "" + $('#target').val() + ""}, function(data){
          if(data.length > 0) {
            $('#suggestions').show();
            $('#autoSuggestionsList').html(data);
          }else{
            $('#suggestions').hide();
          }
        });
        console.log('target');
    };
    
  });