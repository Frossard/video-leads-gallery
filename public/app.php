<?php
if(!defined('ABSPATH')){
    die(__('Access Denied', 'leads-gallery'));
}

/* Ajax INICIO */
add_action( 'wp_ajax_video_leads_gallery', 'my_action_callback' );
add_action( 'wp_ajax_nopriv_video_leads_gallery', 'my_action_callback' );

function my_action_callback()
{
    $lead = leads_gallery_setObj_lead($_POST);
    
    if(leads_gallery_insert_leads($lead)){
        
        $_SESSION['video_leads_gallery_register'] = 'OK';
        
        $json = array(
            'retorno' => 0
        );
        
    }else{
        
        $json = array(
            'retorno' => 1,
            'error' => __('Oops! Something wrong happened.', 'leads-gallery')
        );
    }
    
    echo json_encode($json);
    die;
}
/* Ajax FIM */

# Shotcode
function leads_gallery_shortcode($attrs)
{    
    global $leads_gallery_config;
    
    $embed = leads_gallery_getValue_base64($leads_gallery_config, 'ds_embed');
    $largura = leads_gallery_getValue($leads_gallery_config, 'ds_largura');
    $altura = leads_gallery_getValue($leads_gallery_config, 'ds_altura');
    
    $is_lead = leads_gallery_getValue($leads_gallery_config, 'fl_leads');
    $fb_id = leads_gallery_getValue($leads_gallery_config, 'id_fb');
    
    extract( shortcode_atts(array(
        'style' => ''
    ), $attrs));
    
    #custom style from user
    $style = 'width:' . $largura . 'px; height:' . $altura . 'px; ' . $style;
    
    #Template for embed without lead capture
    $template = file_get_contents(plugin_dir_url( __FILE__ ) . 'templates/default.html');
    #Replacing keywords
    $template = str_replace(array('#STYLE#', '#EMBED#'), array($style, $embed), $template);
    
    
    /* Charging templates */
    if(($is_lead == 1) && empty($_SESSION['video_leads_gallery_register']))
    {
        #Template for lead capture
        $leads = file_get_contents(plugin_dir_url( __FILE__ ) . 'templates/leads.html');
        
        #Scripts from Facebook API
        $script_fb = file_get_contents(plugin_dir_url( __FILE__ ) . 'templates/fb.html');
        #Replacing keywords
        $script_fb = str_replace(array('#FB_ID#', '#LANG#', '#AJAX#'), array($fb_id, __('en_US', 'leads-gallery'), leads_gallery_cadastro_ajax()), $script_fb);
        
        $template = str_replace('#LEADS#', $leads, $template);
        $template .= $script_fb;
        
    }else{
        $template = str_replace('#LEADS#', '', $template);
    }
        
    return $template;
}

function leads_gallery_setObj_lead($post)
{
    $lead = new stdClass();
    
    $lead->ds_name = addslashes($post['name']);
    $lead->ds_email = addslashes($post['email']);
    
    return $lead;
}

function leads_gallery_insert_leads($lead)
{
    global $wpdb;
    
    if(leads_gallery_insert_verify_lead($lead))
    {
        $wpdb->insert( 
            'wp_leads_list', 
            array( 
                'ds_name' => $lead->ds_name, 
                'ds_email' => $lead->ds_email,
                'dt_cadastro' => date('Y-m-d h:i:s')
            ), 
            array( 
                '%s', 
                '%s',
                '%s'
            ) 
        );
    }
    
    return true;
}

function leads_gallery_insert_verify_lead($lead)
{
    global $wpdb;
    
    $rows = $wpdb->get_results( "SELECT id FROM wp_leads_list WHERE ds_email = '" . $lead->ds_email . "' ORDER BY id DESC" );
    
    if(!empty($rows))
    {
        foreach ($rows as $row)
        {
            if(!empty($row->id)){ return false;}
        }
        return true;
    }
    return true;
}

function leads_gallery_cadastro_ajax()
{
    return "jQuery.ajax({
        url: '" . admin_url( 'admin-ajax.php' ) . "',
        type: 'POST',
        data: {
            action: 'video_leads_gallery',
            name: response.name,
            email: response.email
        },
        success: function (result) {
            var json = JSON.parse(result);
            if (json.retorno === 0){
                jQuery('.video-leads-gallery-middle').fadeOut('slow');
            }
        }
    });";
}