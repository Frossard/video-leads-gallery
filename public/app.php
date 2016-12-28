<?php
if(!defined('ABSPATH')){
    die(__('Access Denied', 'leads-gallery'));
}

if($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['e'] == 'L')
{
    $lead = leads_gallery_setObj_lead($_POST);
    leads_gallery_insert_leads($lead);
    
    return true;
}

# Shotcode
function leads_gallery_shortcode($attrs){
    
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
    if($is_lead == 1)
    {
        #Template for lead capture
        $leads = file_get_contents(plugin_dir_url( __FILE__ ) . 'templates/leads.html');
        
        #Scripts from Facebook API
        $script_fb = file_get_contents(plugin_dir_url( __FILE__ ) . 'templates/fb.html');
        #Replacing keywords
        $script_fb = str_replace(array('#FB_ID#', '#LANG#'), array($fb_id, __('en_US', 'leads-gallery')), $script_fb);
        
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
    
    $lead->ds_name = addslashes($post['video-leads-gallery-name']);
    $lead->ds_email = addslashes($post['video-leads-gallery-email']);
    
    return $lead;
}

function leads_gallery_insert_leads($lead)
{
    global $wpdb;
    
    $wpdb->insert( 
	'wp_lead_list', 
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