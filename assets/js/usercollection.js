$(document).ready(function () {
    $('#add-traveler').click(function(){
        const index= $('#travelers_travelers div.group').length;
       const tmpl = $('#travelers_travelers').data('prototype').replace(/__name__/g, index);
       $('#travelers_travelers').append(tmpl);
       
    })
});