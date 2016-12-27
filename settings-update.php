<?php
if(!defined('ABSPATH')){
    die(__('Access Denied', 'leads-gallery'));
}

$c = leads_gallery_recoverId();
if($c != false){
    $leads_gallery_config = $c;
}


if(isset($_POST['e']) && $_POST['e'] == 'settings'){
    
    $leads_gallery_message = leads_gallery_updateSettings($_POST);
}

function leads_gallery_updateSettings($post)
{
    $recoveredConfig = leads_gallery_recoverId();
    $config = leads_gallery_setObj($post);
    $config->id = $recoveredConfig->id;
    
    if($config->id != false)
    {
        leads_gallery_update($config);
        
        $message = __('Settings updated successfully!', 'leads-gallery');
        
    }  else {
        
        leads_gallery_insert($config);
        
        $message = __('Settings saved successfully!', 'leads-gallery');
    }
    
    return $message;
}

function leads_gallery_setObj($post)
{
    $config = new stdClass();
    
    $config->ds_embed = base64_encode($post['ds_embed']);
    $config->ds_largura = addslashes($post['ds_largura']);
    $config->ds_altura = addslashes($post['ds_altura']);
    $config->fl_leads = isset($post['fl_leads']) ? addslashes($post['fl_leads']) : 0;
    $config->ds_imagem = !empty($config->ds_imagem) ? addslashes($post['ds_imagem']) : NULL;
    $config->id_fb = addslashes($post['id_fb']);
    $config->ds_fb_key = addslashes($post['ds_fb_key']);
    
    return $config;
}

function leads_gallery_recoverId()
{
    global $wpdb;
    
    $rows = $wpdb->get_results( "SELECT * FROM wp_leads_config ORDER BY id DESC" );
    
    foreach ($rows as $row)
    {
        $id = $row->id;
        $config = $row;
        break;
    }
    
    return isset($id) ? $config : false;
}

function leads_gallery_insert($config)
{
    global $wpdb;
    
    $wpdb->insert( 
	'wp_leads_config', 
	array( 
            'ds_embed' => !empty($config->ds_embed) ? $config->ds_embed : NULL,
            'ds_largura' => !empty($config->ds_largura) ? $config->ds_largura : NULL,
            'ds_altura' => !empty($config->ds_altura) ? $config->ds_altura : NULL,
            'fl_leads' => !empty($config->fl_leads) ? $config->fl_leads : NULL,
            'ds_imagem' => !empty($config->ds_imagem) ? $config->ds_imagem : NULL,
            'id_fb' => !empty($config->id_fb) ? $config->id_fb : NULL,
            'ds_fb_key' => !empty($config->ds_fb_key) ? $config->ds_fb_key : NULL,
	), 
	array( 
            '%s', 
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%s'
	) 
    );
}

function leads_gallery_update($config)
{
    global $wpdb;
    
    $wpdb->update( 
        'wp_leads_config', 
        array( 
            'ds_embed' => !empty($config->ds_embed) ? $config->ds_embed : NULL,
            'ds_largura' => !empty($config->ds_largura) ? $config->ds_largura : NULL,
            'ds_altura' => !empty($config->ds_altura) ? $config->ds_altura : NULL,
            'fl_leads' => !empty($config->fl_leads) ? $config->fl_leads : NULL,
            'ds_imagem' => !empty($config->ds_imagem) ? $config->ds_imagem : NULL,
            'id_fb' => !empty($config->id_fb) ? $config->id_fb : NULL,
            'ds_fb_key' => !empty($config->ds_fb_key) ? $config->ds_fb_key : NULL,
	), 
        array( 'ID' => $config->id ), 
        array( 
            '%s', 
            '%s',
            '%s',
            '%d',
            '%s',
            '%s',
            '%s'
	), 
        array( '%d' ) 
    );
}

function leads_gallery_getValue($a, $attr){
    if(!empty($a))
        return $a->$attr;
    return '';
}

function leads_gallery_getValue_base64($a, $attr){
    if(!empty($a))
        return base64_decode($a->$attr);
    return '';
}

//date('Y-m-d h:i:s')
