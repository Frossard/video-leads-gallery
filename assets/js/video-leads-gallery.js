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
        
        video_leads_gallery_cadastro(name, email);
    });
});

function video_leads_gallery_cadastro(name, email)
{
    jQuery.ajax({
        url: ajax_object.ajax_url,
        type: "POST",
        data: {
            action: 'video_leads_gallery',
            name: name,
            email: email
        },
        success: function (result) {
            var json = JSON.parse(result);
            if (json.retorno === 0){
                jQuery(".video-leads-gallery-middle").fadeOut('slow');
            }
        },
        complete: function () {
        }
    });
}