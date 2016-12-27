<?php
/**
 * Plugin Name: Video Leads gallery
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
    die('Acesso Negado!');
}

# Habilitando a tradução
function leads_gallery_load_textdomain() {
    load_plugin_textdomain('leads-gallery', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('plugins_loaded', 'leads_gallery_load_textdomain');

global $leads_gallery_version;
$leads_gallery_version = '1.0';

#Ambiente no Wp-Admin
require_once ('../wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/install.php');
require_once ('../wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/settings.php');



