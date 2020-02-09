// add-collection-widget.js
$(document).ready(function () {
    
    $('.add-another-collection-widget').click(function (e) {
        var list = $($(this).attr('data-list-selector'));
        
        var counter = list.data('widget-counter') || list.children().length;
  
        var newWidget = list.attr('data-prototype');        

        newWidget = newWidget.replace(/__name__/g, counter);
        
        newWidget += '<button type="button" class="waves-effect waves-light btn red darken-4 delete">Delete</button>';
        
        counter++;

        list.data('widget-counter', counter);

        var newElem = $(list.attr('data-widget-tags')).html(newWidget);
        newElem.appendTo(list);

        handleDelete()
    });

        collectionHolder = $('ul#traveler-fields-list');
        collectionHolder.find('li').each(function() {
        
        addPictureFormDeleteLink($(this));
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
        var $removeFormButton = $('<button type="button" class="delete btn btn-danger">Delete</button>');
        $travelerFormLi.append($removeFormButton);

        $removeFormButton.on('click', function(e) {
            // remove the li for the Picture form
            $travelerFormLi.remove();
        });
    }

});