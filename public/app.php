<?php
if(!defined('ABSPATH')){
    die(__('Access Denied', 'leads-gallery'));
}

/* Ajax INICIO */
add_action( 'wp_ajax_video_leads_gallery', 'leads_gallery_my_action_callback' );
add_action( 'wp_ajax_nopriv_video_leads_gallery', 'leads_gallery_my_action_callback' );

function leads_gallery_my_action_callback()
{
    $lead = leads_gallery_setObj_lead($_POST);
    
    if(leads_gallery_insert_leads($lead)){
        
        #Cadastro por e-mail
        if($lead->fl_origin == 2){
            #Token to verify
            $token = leads_gallery_generate_token($lead);
            
            #Send activation email
            leads_gallery_send_email($lead, $token, $_POST['path']);
            
        }else{
            #Cadastro por Facebook
            $_SESSION['video_leads_gallery_register'] = 'OK';
        }
        
        $json = array(
            'return' => 0,
            'origin' => $lead->fl_origin
        );
        
    }else{
        /* Se entrar aqui é porque já está cadastrado. */
        $_SESSION['video_leads_gallery_register'] = 'OK';
        
        $json = array(
            'return' => 1,
            'error' => __('E-mail already registered!', 'leads-gallery')
        );
    }
    
    echo json_encode($json);
    die;
}
/* Ajax FIM */

# Shotcode
function leads_gallery_shortcode($attrs)
{    
    global $leads_gallery_playlist;
    global $leads_gallery_facebook;
    
    extract( shortcode_atts(array(
        'id' => '',
        'style' => ''
    ), $attrs));
        
    #Validando token
    if(isset($_GET['leads_gallery_token']))
    {
        if(leads_gallery_verify_token($_GET['leads_gallery_token']))
        {
            $_SESSION['video_leads_gallery_register'] = 'OK';
        }
    }
    
    $leads_gallery_playlist = leads_gallery_playlist_recoverId($id);
    
    $embed = leads_gallery_getValue_base64($leads_gallery_playlist, 'ds_embed');
    $width = leads_gallery_getValue($leads_gallery_playlist, 'ds_width');
    $height = leads_gallery_getValue($leads_gallery_playlist, 'ds_height');
    $is_lead = leads_gallery_getValue($leads_gallery_playlist, 'fl_leads');
    $id_facebook = leads_gallery_getValue($leads_gallery_facebook, 'id_facebook');
    
        
    #custom style from user
    $style = 'width: ' . $width . '; height: ' . $height . '; ' . $style;
    
    /* SSL File Get Contents */
    $arrContextOptions = leads_gallery_contextOptions();
    
    #Template for embed without lead capture
    $template = file_get_contents(plugin_dir_url( __FILE__ ) . 'templates/default.html', false, stream_context_create($arrContextOptions));
    #Replacing keywords
    $template = str_replace(array('#STYLE#', '#EMBED#'), array($style, $embed), $template);
    
    
    /* Charging templates */
    if(($is_lead == 1) && empty($_SESSION['video_leads_gallery_register']))
    {
        #Template for lead capture
        $leads = file_get_contents(plugin_dir_url( __FILE__ ) . 'templates/leads.html', false, stream_context_create($arrContextOptions));
        
        #Scripts from Facebook API
        $script_fb = file_get_contents(plugin_dir_url( __FILE__ ) . 'templates/fb.html', false, stream_context_create($arrContextOptions));
        #Replacing keywords
        $script_fb = str_replace(array('#FB_ID#', '#LANG#', '#AJAX#'), array($id_facebook, __('en_US', 'leads-gallery'), leads_gallery_cadastro_ajax()), $script_fb);
        
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
    $lead->fl_origin = addslashes($post['origin']);
    
    return $lead;
}

function leads_gallery_insert_leads($lead)
{
    global $wpdb;
    /* verifico se o lead existe, se existir retorno false */
    if(leads_gallery_insert_verify_lead($lead))
    {
         #Cadastro por e-mail
        if($lead->fl_origin == 2){
            $fl_active = 2;
        }else{
            $fl_active = 1;
        }
        
        $wpdb->insert( 
            'wp_leads_list', 
            array( 
                'ds_name' => $lead->ds_name, 
                'ds_email' => $lead->ds_email,
                'dt_created' => date('Y-m-d h:i:s'),
                'fl_origin' => $lead->fl_origin,
                'fl_active' => $fl_active
            ), 
            array( 
                '%s', 
                '%s',
                '%s',
                '%d',
                '%d'
            ) 
        );
        return true;
        
    }else{
        return false;
    }   
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

/* Enable on facebook login */
function leads_gallery_cadastro_ajax()
{
    return "jQuery.ajax({
        url: '" . admin_url( 'admin-ajax.php' ) . "',
        type: 'POST',
        data: {
            action: 'video_leads_gallery',
            name: response.name,
            email: response.email,
            origin: 1
        },
        success: function (result) {
            var json = JSON.parse(result);
            if (json.return === 0){
                jQuery('.video-leads-gallery-middle').fadeOut('slow');
            }
        }
    });";
}

function leads_gallery_send_email($lead, $token, $path)
{
    $sitename = get_bloginfo( 'name' );
    $subject = __('Register confirmation', 'leads-gallery');
    
    $message = '<p>' . __('Please click the link below to complete your registration:', 'leads-gallery') . '<br/><br/><a href="' . $path . '?leads_gallery_token=' . $token . '">' . $path . '</a></p>';
    
    $headers = array();
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers[] = 'From: ' . $sitename . ' <' . get_bloginfo( 'admin_email' ) . '>';
        
    /* SSL File Get Contents */
    $arrContextOptions = leads_gallery_contextOptions();
    #Template for embed without lead capture
    $template = file_get_contents(plugin_dir_url(__FILE__) . 'templates/email.html', false, stream_context_create($arrContextOptions));
    #Replacing keywords
    $template = str_replace(array('#TITLE#', '#CONTENT#'), array($subject, $message), $template);

    $subject .= ' - ' . $sitename;
    
    wp_mail( $lead->ds_email, $subject, $template, $headers );
}

function leads_gallery_generate_token($lead)
{
    global $wpdb;
    $token = uniqid();
    
    $wpdb->update( 
        'wp_leads_list', 
        array( 
            'ds_token' => $token
	), 
        array( 'ds_email' => $lead->ds_email ), 
        array( 
            '%s'
	), 
        array( '%s' ) 
    );
    
    return $token;
    
}

function leads_gallery_verify_token($token)
{
    global $wpdb;
    
    $rows = $wpdb->get_results( "SELECT id FROM wp_leads_list WHERE ds_token = '" . addslashes($token) . "' ORDER BY id DESC" );
    
    if(!empty($rows))
    {
        foreach ($rows as $row)
        {
            if(!empty($row->id))
            { 
                lead_gallery_active_email($row->id);
                
                return true;
            }
        }
    }
    return false;
}

function lead_gallery_active_email($id)
{
    global $wpdb;
    
    $wpdb->update( 
        'wp_leads_list', 
        array( 
            'fl_active' => 1
        ), 
        array( 'ID' => $id ), 
        array( 
            '%d'
        ), 
        array( '%d' ) 
    );
}

function leads_gallery_contextOptions()
{
    /* SSL File Get Contents */
    $arrContextOptions = array(
        "ssl" => array(
            "verify_peer" => false,
            "verify_peer_name" => false,
        ),
    );
    
    return $arrContextOptions;
}