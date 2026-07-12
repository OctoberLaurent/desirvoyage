$(document).ready(function () {
    const $collectionHolder = $('#traveler-fields-list');

    function addTravelerFormDeleteLink($travelerFormLi) {
        const $removeFormButton = $('<button type="button" class="waves-effect waves-light btn red darken-4 delete">Effacer</button>');
        $travelerFormLi.append($removeFormButton);

        $removeFormButton.on('click', function () {
            $travelerFormLi.remove();
        });
    }

    function addTraveler($list) {
        const configuredCounter = Number.parseInt($list.attr('data-widget-counter'), 10);
        const counter = Number.isNaN(configuredCounter) ? $list.children().length : configuredCounter;
        const prototype = $list.attr('data-prototype').replace(/__name__/g, counter);
        const $newTraveler = $($list.attr('data-widget-tags')).html(prototype);

        $newTraveler.appendTo($list);
        $list.attr('data-widget-counter', counter + 1);
        addTravelerFormDeleteLink($newTraveler);

        return $newTraveler;
    }

    $collectionHolder.children('li').each(function () {
        addTravelerFormDeleteLink($(this));
    });

    $('.add-another-collection-widget').on('click', function (event) {
        event.preventDefault();
        addTraveler($($(this).attr('data-list-selector')));
    });

    $('#add-user-in-traveler').on('click', function (event) {
        event.preventDefault();

        const $button = $(this);
        if ($button.prop('disabled')) {
            return;
        }

        const $traveler = addTraveler($collectionHolder);
        $traveler.find('input[name$="[lastname]"]').val($button.attr('data-buyer-lastname'));
        $traveler.find('input[name$="[firstname]"]').val($button.attr('data-buyer-firstname'));
        $traveler.find('input[name$="[email]"]').val($button.attr('data-buyer-email'));
        $traveler.find('input[name$="[birthday]"]').val($button.attr('data-buyer-birthday'));
        $button.prop('disabled', true);
    });
});
