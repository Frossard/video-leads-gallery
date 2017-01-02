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

add_action('init', 'leads_gallery_load_session', 1);

function leads_gallery_load_session() 
{
    if(!session_id()) {
        session_start();
    }
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

#Ambiente Front-end
require_once (ABSPATH . '/wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/public/app.php');

function leads_gallery_load_assets()
{
    wp_enqueue_style( 'video-leads-gallery', '/wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/assets/css/video-leads-gallery.css' );
    
    wp_register_script( 'video-leads-gallery-placeholder', '/wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/assets/js/jquery.placeholder.js', array('jquery'), '1.0.0', false );
    wp_register_script( 'video-leads-gallery', '/wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/assets/js/video-leads-gallery.js', array('jquery'), '1.0.0', false );
    
    $ajax_object = array('ajax_url' => admin_url( 'admin-ajax.php' ));
    wp_localize_script( 'video-leads-gallery', 'ajax_object', $ajax_object );
    
    wp_enqueue_script('video-leads-gallery-placeholder');
    wp_enqueue_script('video-leads-gallery');
}

add_action( 'wp_enqueue_scripts', 'leads_gallery_load_assets' );

add_shortcode('video_leads_gallery', 'leads_gallery_shortcode');
