jQuery(function () {
    jQuery(".userbtn").on('click', function(e){
        e.preventDefault();
        var ajaxurl =  jsonplaceholder_params.ajaxurl; // AJAX handler
        var data = {
            'action': 'userdetail',
            'user_id': jQuery(this).attr('rel')
        };
        jQuery.post(ajaxurl, data, function(response) {
            jQuery(".table").html(response );
        });
    });
    
});