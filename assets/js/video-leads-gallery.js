jQuery(function(){
    jQuery(".video-leads-gallery-front").click(function(){
        jQuery(this).hide(function(){
            jQuery(this).next(".video-leads-gallery-middle").fadeIn('slow');
        });
    });
    
    jQuery("#video-leads-gallery-salvar").click(function(e){
        e.preventDefault();
        
        var name = jQuery("#video-leads-gallery-name").val();
        var email = jQuery("#video-leads-gallery-email").val();
        
        jQuery.ajax({
            url: 'http://' + window.location.hostname + '/darwinenem/wp-content/plugins/wp-plugin-video-leads-gallery/public/app.php',
            type: "POST",
            success: function (result) {
                var json = JSON.parse(result);
                if (json.retorno === 0){
                    
                }
            },
            complete: function () {
            }
        });
    });
});