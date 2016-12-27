<?php
/*WordPress Menus API.
* Adicionando a opção do menu 
*/
if(!defined('ABSPATH')){
    die('Acesso Negado!');
}

function add_new_menu_items()
{
    add_menu_page(
        "Leads Gallery",
        "Leads Gallery",
        "manage_options",
        "leads-gallery",
        "leads_gallery_page",
        '../wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/assets/img/icon-yt.png', //Optional. The URL to the menu item icon.
        20 //Optional. Position of the menu item in the menu.
    );
}

function leads_gallery_page()
{
    ?>
        <div class="wrap">
        <div id="icon-options-general" class="icon32"></div>

        <!-- run the settings_errors() function here. -->
        <?php settings_errors(); ?>

        <h1>Video Leads Gallery</h1>

        <?php
            $active_tab = "header-options";
            if(isset($_GET["tab"]))
            {
                if($_GET["tab"] == "header-options")
                {
                    $active_tab = "header-options";
                }
                else
                {
                    $active_tab = "ads-options";
                }
            }
        ?>

        <h2 class="nav-tab-wrapper">
            <a href="?page=leads-gallery&tab=header-options" class="nav-tab <?php if($active_tab == 'header-options'){echo 'nav-tab-active';} ?> "><?php _e('Settings', 'leads-gallery'); ?></a>
            <a href="?page=leads-gallery&tab=ads-options" class="nav-tab <?php if($active_tab == 'ads-options'){echo 'nav-tab-active';} ?>"><?php _e('List of Leads', 'leads-gallery'); ?></a>
        </h2>

        <form method="post" action="options.php" enctype="multipart/form-data">
            <?php

                settings_fields("header_section");

                do_settings_sections("leads-gallery");

                submit_button(); 

            ?>          
        </form>
    </div>
    <?php
}

add_action("admin_menu", "add_new_menu_items");

function display_options()
{
    if(isset($_GET["tab"]))
    {
        if($_GET["tab"] == "header-options")
        {
            add_settings_section("header_section", __('Youtube Playlist', 'leads-gallery'), "display_header_options_content", "leads-gallery");

            add_settings_field("header_logo", "Logo Url", "display_logo_form_element", "leads-gallery", "header_section");
            register_setting("header_section", "header_logo");

            add_settings_field("background_picture", __('Background Image', 'leads-gallery'), "background_form_element", "leads-gallery", "header_section");
            register_setting("header_section", "background_picture", "handle_file_upload");
        }
        else
        {
            add_settings_section("header_section", __('Registered Leads', 'leads-gallery'), "display_advertising_options_content", "leads-gallery");

            add_settings_field("advertising_code", "Ads Code", "display_ads_form_element", "leads-gallery", "header_section");      
            register_setting("header_section", "advertising_code");
        }
    }
    else
    {
        add_settings_section("header_section", __('Youtube Playlist', 'leads-gallery'), "display_header_options_content", "leads-gallery");

        add_settings_field("header_logo", "Logo Url", "display_logo_form_element", "leads-gallery", "header_section");
        register_setting("header_section", "header_logo");

        add_settings_field("background_picture", __('Background Image', 'leads-gallery'), "background_form_element", "leads-gallery", "header_section");
        register_setting("header_section", "background_picture", "handle_file_upload");
    }

}

function handle_file_upload($options)
{
    if(!empty($_FILES["background_picture"]["tmp_name"]))
    {
        $urls = wp_handle_upload($_FILES["background_picture"], array('test_form' => FALSE));
        $temp = $urls["url"];
        return $temp;   
    }

    return get_option("background_picture");
}


function display_header_options_content(){
    _e('Tab 1 description', 'leads-gallery');
}

function display_advertising_options_content(){
    _e('Tab 2 description', 'leads-gallery');
}

# Imagem de background
function background_form_element()
{
    ?>
        <input type="file" name="background_picture" id="background_picture" value="<?php echo get_option('background_picture'); ?>" />
        <?php echo get_option("background_picture"); ?>
    <?php
}

function display_logo_form_element()
{
    ?>
        <input type="text" name="header_logo" id="header_logo" value="<?php echo get_option('header_logo'); ?>" />
    <?php
}

function display_ads_form_element()
{
    ?>
        <input type="text" name="advertising_code" id="advertising_code" value="<?php echo get_option('advertising_code'); ?>" />
    <?php
}

add_action("admin_init", "display_options");

//add_action("admin_init", "teste_leads");
//
//function teste_leads()
//{
//    global $wpdb;
//    
//    $wpdb->insert( 
//	'wp_lead_list', 
//	array( 
//            'ds_name' => 'Victor Frossard', 
//            'ds_email' => 'victor.frossard@uvv.br' 
//	), 
//	array( 
//            '%s', 
//            '%s' 
//	) 
//    );
//}