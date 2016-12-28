<?php
/**
 * Plugin Name: Video Leads Gallery
 * Plugin URI: https://github.com/Frossard/video-leads-gallery
 * Description: Wordpress video gallery who takes leads before playing. It uses Youtube playlist to play videos.
 * Version: 0.0.1
 * Author: Victor Frossard
 * Author URI: http://victorfrossard.com.br/
 * License: GPL
 * Text Domain: leads-gallery
 * Domain Path: languages/
 */

# Verificação de segurança
if(!defined('ABSPATH')){
    die(__('Access Denied', 'leads-gallery'));
}

# Habilitando a tradução
function leads_gallery_load_textdomain() {
    load_plugin_textdomain('leads-gallery', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'leads_gallery_load_textdomain');

global $leads_gallery_config; //Object settings
global $leads_gallery_message; //Success message
global $leads_gallery_error; //Error message
global $leads_gallery_version;
$leads_gallery_version = '1.0';

#Instalação do banco de dados
require_once (ABSPATH . '/wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/install.php');

#Ambiente no Wp-Admin
require_once (ABSPATH . '/wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/settings-update.php');
require_once (ABSPATH . '/wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/settings.php');


# Shotcode
function leads_gallery_shortcode($attrs){
    
    global $leads_gallery_config;
    
    $largura = leads_gallery_getValue($leads_gallery_config, 'ds_largura');
    $altura = leads_gallery_getValue($leads_gallery_config, 'ds_altura');
    
    extract( shortcode_atts(array(
        'style' => ''
    ), $attrs));
    
    return '<div class="video-leads-gallery" style="width:' . $largura . 'px; height:' . $altura . 'px; ' . $style . '">' . leads_gallery_getValue_base64($leads_gallery_config, 'ds_embed') . '</div>';
}

add_shortcode('video_leads_gallery', 'leads_gallery_shortcode');
