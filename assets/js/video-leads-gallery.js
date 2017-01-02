jQuery(function(){
    jQuery.validateEmail=function(email){
        er=/^[a-zA-Z0-9][a-zA-Z0-9\._-]+@([a-zA-Z0-9\._-]+\.)[a-zA-Z-0-9]{2}/;
        
        if(er.exec(email))
            return true;
        else
            return false;
    };
    
    jQuery(".video-leads-gallery-front").click(function(){
        jQuery(this).hide(function(){
            jQuery(this).next(".video-leads-gallery-middle").fadeIn('slow');
        });
    });
    
    jQuery("#video-leads-gallery-enable-email").click(function(){
        jQuery(this).fadeOut(function(){
            jQuery(this).next('form').fadeIn();
        });
    });
    
    jQuery("#video-leads-gallery-salvar").click(function(e){
        e.preventDefault();
        
        var input_name = jQuery("#video-leads-gallery-name");
        var input_email = jQuery("#video-leads-gallery-email");
        
        var name = input_name.val();
        var email = input_email.val();
        
        if(name == ""){
            input_name.attr("placeholder","Nome: Campo obrigatório!").addClass("placeholder_erro").focus();
            return false;
        }else{
            input_name.removeClass("placeholder_erro").attr("placeholder","nome");
        }
                
        if(email == ""){
            input_email.attr("placeholder","E-mail: Campo obrigatório!").addClass("placeholder_erro").focus();
            return false;
        }else 
            if(!jQuery.validateEmail(email)){
            input_email.val("");
            input_email.attr("placeholder","Digite um e-mail válido.").addClass("placeholder_erro").focus();
            return false;
        }
        else{
            input_email.removeClass("placeholder_erro").attr("placeholder","e-mail");
        }
        
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
        }
    });
}