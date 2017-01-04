<?php
/* Instalação do Plugin */
if(!defined('ABSPATH')){
    die(__('Access Denied', 'leads-gallery'));
}

register_activation_hook( __FILE__, 'leads_gallery_install' );

function leads_gallery_install () 
{
    global $wpdb;
    global $leads_gallery_version;
    
    $installed_ver = get_option( "leads_gallery_version" );
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE " . $wpdb->prefix . "leads_facebook (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        id_facebook varchar(250) NOT NULL,
        PRIMARY KEY  (id)
    ) $charset_collate;
        
    CREATE TABLE " . $wpdb->prefix . "leads_playlists (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        ds_name text,
        ds_embed text NOT NULL,
        ds_width varchar(55),
        ds_height varchar(55),
        dt_created DATETIME,
        fl_leads boolean,
        PRIMARY KEY  (id)
    ) $charset_collate;

    CREATE TABLE " . $wpdb->prefix . "leads_list (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        ds_name varchar(250),
        ds_email varchar(300) NOT NULL,
        dt_created DATETIME,
        fl_active boolean,
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
    
    if ( $installed_ver != $leads_gallery_version ) 
    {
        update_option( "leads_gallery_version", $leads_gallery_version );
    }else{
        add_option( 'leads_gallery_version', $leads_gallery_version );
    }
}

function leads_gallery_update_db_check() 
{
    global $leads_gallery_version;
    if ( get_site_option( 'leads_gallery_version' ) != $leads_gallery_version ) {
        leads_gallery_install();
    }
}

add_action( 'plugins_loaded', 'leads_gallery_update_db_check' );