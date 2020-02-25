// add-collection-widget.js
$(document).ready(function () {
    global.counter = 0;
    
    $('.add-another-collection-widget').click(function (e) {

        var list = $($(this).attr('data-list-selector'));
        
        var counter = list.data('widget-counter') || list.children().length;
  
        var newWidget = list.attr('data-prototype');

        newWidget = newWidget.replace(/__name__/g, counter);
        
        newWidget += '<button type="button" class="waves-effect waves-light btn red darken-4 delete">Effacer</button>';
        
        global.counter = counter
        counter++;
       
        list.data('widget-counter', counter);

        var newElem = $(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);

        handleDelete()

    });

        collectionHolder = $('ul#traveler-fields-list');
        collectionHolder.find('li').each(function() {
        
        addTravelerFormDeleteLink($(this));
    });

    $('#add-user-in-traveler').click(function (e) {

        
        var civilityUser = [];
        $('#add-traveler').click();
        $('td').each(function( index ) {
        civilityUser.push(($( this ).text()));
        });
        $('#travelers_travelers_'+global.counter+'_lastname').val(civilityUser[0]) 
        $('#travelers_travelers_'+global.counter+'_firstname').val(civilityUser[1]) 
        $('#travelers_travelers_'+global.counter+'_email').val(civilityUser[2]) 
        $('#travelers_travelers_'+global.counter+'_birthday').val(formatDate(civilityUser[3]))
        $('#add-user-in-traveler').attr("disabled", true);
    });
   

    function handleDelete(){

        var elements = document.getElementsByClassName('delete');
        elements.forEach(element => {
             element.addEventListener('click', function (event) {
                    this.parentNode.remove();
                    });
                        });
        
    }
        
    function addTravelerFormDeleteLink($travelerFormLi) {
        var $removeFormButton = $('<button type="button" class="waves-effect waves-light btn red darken-4 delete">Effacer</button>');
        $travelerFormLi.append($removeFormButton);

        $removeFormButton.on('click', function(e) {
            // remove the li for the Picture form
            $travelerFormLi.remove();
        });
    }

    function formatDate(date){

        var format = date.split(/\D/);
        return format.reverse().join('-');
    }
    
});