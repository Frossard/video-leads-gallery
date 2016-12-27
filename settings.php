<?php
if(!defined('ABSPATH')){
    die(__('Access Denied', 'leads-gallery'));
}

/* Add a admin menu option */
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

        <h1>Video Leads Gallery</h1>
        
        <!-- run the settings_errors() function here. -->
        <?php settings_errors(); ?>

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
                    $active_tab = "leads-list";
                }
            }
        ?>

        <h2 class="nav-tab-wrapper">
            <a href="?page=leads-gallery&tab=header-options" class="nav-tab <?php if($active_tab == 'header-options'){echo 'nav-tab-active';} ?> "><?php _e('Settings', 'leads-gallery'); ?></a>
            <a href="?page=leads-gallery&tab=leads-list" class="nav-tab <?php if($active_tab == 'leads-list'){echo 'nav-tab-active';} ?>"><?php _e('List of Leads', 'leads-gallery'); ?></a>
        </h2>

        <?php 
        if(isset($_GET["tab"]) && $_GET["tab"] == "leads-list")
        { 
            settings_fields("list_section");
            do_settings_sections("leads-gallery");
            leads_gallery_display_table();
            
        }else{
        ?>
        
        <form method="post" action="options.php" enctype="multipart/form-data">
        <?php    
                settings_fields("playlist_section");
                settings_fields("leads_section");
                do_settings_sections("leads-gallery");
                submit_button();
        ?>
        </form>  
        <?php  
            }
        ?>     
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
            /* Playlist Youtube */
            
            add_settings_section("playlist_section", __('Youtube Playlist', 'leads-gallery'), "leads_gallery_display_playlist_description", "leads-gallery");

            add_settings_field("ds_embed", __('Playlist Embed', 'leads-gallery'), "display_embed_form_element", "leads-gallery", "playlist_section");
            register_setting("playlist_section", "ds_embed");
            
            add_settings_field("ds_largura", __('Width', 'leads-gallery'), "display_largura_form_element", "leads-gallery", "playlist_section");
            register_setting("playlist_section", "ds_largura");
            
            add_settings_field("ds_altura", __('Height', 'leads-gallery'), "display_altura_form_element", "leads-gallery", "playlist_section");
            register_setting("playlist_section", "ds_altura");
            
            add_settings_field("fl_leads", __('Enable Leads Captation', 'leads-gallery'), "display_lead_form_element", "leads-gallery", "playlist_section");
            register_setting("playlist_section", "fl_leads");

            /* Captação de Leads */
            
            add_settings_section("leads_section", __('Captation settings', 'leads-gallery'), "leads_gallery_display_leads_description", "leads-gallery");
            
            add_settings_field("ds_imagem", __('Background Image', 'leads-gallery'), "background_form_element", "leads-gallery", "leads_section");
            register_setting("leads_section", "ds_imagem", "handle_file_upload");
            
            add_settings_field("id_fb", __('Facebook App ID', 'leads-gallery'), "display_id_form_element", "leads-gallery", "leads_section");
            register_setting("leads_section", "id_fb");
            
            add_settings_field("ds_fb_key", __('Facebook App Secret Key', 'leads-gallery'), "display_key_form_element", "leads-gallery", "leads_section");
            register_setting("leads_section", "ds_fb_key");
        }
        else
        {
            add_settings_section("list_section", __('Registered Leads', 'leads-gallery'), "leads_gallery_display_list_description", "leads-gallery");
        }
    }
    else
    {
         /* Playlist Youtube */
            
        add_settings_section("playlist_section", __('Youtube Playlist', 'leads-gallery'), "leads_gallery_display_playlist_description", "leads-gallery");

        add_settings_field("ds_embed", __('Playlist Embed', 'leads-gallery'), "display_embed_form_element", "leads-gallery", "playlist_section");
        register_setting("playlist_section", "ds_embed");

        add_settings_field("ds_largura", __('Width', 'leads-gallery'), "display_largura_form_element", "leads-gallery", "playlist_section");
        register_setting("playlist_section", "ds_largura");

        add_settings_field("ds_altura", __('Height', 'leads-gallery'), "display_altura_form_element", "leads-gallery", "playlist_section");
        register_setting("playlist_section", "ds_altura");

        add_settings_field("fl_leads", __('Enable Leads Captation', 'leads-gallery'), "display_lead_form_element", "leads-gallery", "playlist_section");
        register_setting("playlist_section", "fl_leads");

        /* Captação de Leads */

        add_settings_section("leads_section", __('Captation settings', 'leads-gallery'), "leads_gallery_display_leads_description", "leads-gallery");

        add_settings_field("ds_imagem", __('Background Image', 'leads-gallery'), "background_form_element", "leads-gallery", "leads_section");
        register_setting("leads_section", "ds_imagem", "handle_file_upload");

        add_settings_field("id_fb", __('Facebook App ID', 'leads-gallery'), "display_id_form_element", "leads-gallery", "leads_section");
        register_setting("leads_section", "id_fb");

        add_settings_field("ds_fb_key", __('Facebook App Secret Key', 'leads-gallery'), "display_key_form_element", "leads-gallery", "leads_section");
        register_setting("leads_section", "ds_fb_key");
    }

}

/* Tab 1 - Settings */

function leads_gallery_display_playlist_description(){
    _e("First it's necessary set the Youtube playlist and it's vídeo width and height.", 'leads-gallery');
}

function display_embed_form_element()
{
    ?>
        <input type="text" name="ds_embed" id="ds_embed" style="width: 100%;" value="<?php echo get_option('ds_embed'); ?>" />
        <i><xmp>Ex.: <iframe width="560" height="315" src="https://www.youtube.com/embed/r_vt3of4ukw?list=PLMVTEBBvR8MHZ2CKumJEIz3mnNeCnq2QO" frameborder="0" allowfullscreen></iframe></xmp></i>
    <?php
}

function display_largura_form_element()
{
    ?>
        <input type="text" name="ds_largura" id="ds_largura" value="<?php echo get_option('ds_largura'); ?>" placeholder="Ex.: 560" /> <?php _e('(Only numbers)', 'leads-gallery');?>
    <?php
}

function display_altura_form_element()
{
    ?>
        <input type="text" name="ds_altura" id="ds_altura" value="<?php echo get_option('ds_altura'); ?>" placeholder="Ex.: 315" /> <?php _e('(Only numbers)', 'leads-gallery');?>
    <?php
}

function display_lead_form_element()
{
    ?>
        <input type="checkbox" name="fl_leads" id="fl_leads" value="1" <?php echo (get_option('ds_altura') == 1) ? 'checked' : ''; ?>> <?php _e('Yes', 'leads-gallery');?>
    <?php
}

/* ---------------------------- */

function leads_gallery_display_leads_description(){
    _e("After enabled leads captation, it's necessary set the Facebook keys for Facebook login app.", 'leads-gallery');
}

# Imagem de background
function background_form_element()
{
    ?>
        <input type="file" name="ds_imagem" id="ds_imagem" value="<?php echo get_option('ds_imagem'); ?>" />
        <?php echo get_option("ds_imagem"); ?>
    <?php
}

function handle_file_upload($options)
{
    if(!empty($_FILES["ds_imagem"]["tmp_name"]))
    {
        $urls = wp_handle_upload($_FILES["ds_imagem"], array('test_form' => FALSE));
        $temp = $urls["url"];
        return $temp;   
    }

    return get_option("ds_imagem");
}

function display_id_form_element()
{
    ?>
        <input type="text" name="id_fb" id="id_fb" style="width: 100%;" value="<?php echo get_option('id_fb'); ?>" /> <?php _e('(Get on Facebook Login App)', 'leads-gallery');?>
    <?php
}

function display_key_form_element()
{
    ?>
        <input type="text" name="ds_fb_key" id="ds_fb_key" style="width: 100%;" value="<?php echo get_option('ds_fb_key'); ?>" /> <?php _e('(Get on Facebook Login App)', 'leads-gallery');?>
    <?php
}


/* Tab 2 - Leads List */

function leads_gallery_display_list_description(){
    _e('This is the registered leads until today', 'leads-gallery');
    echo '<br/><br/>';
}

function leads_gallery_display_table()
{
    global $wpdb;
    
    $rows = $wpdb->get_results( "SELECT * FROM wp_leads_list ORDER BY id DESC" );
    ?>
    <table class="wp-list-table widefat fixed striped posts" style="max-width:1000px;">
	<thead>
            <tr>
                <th scope="col" class="column-primary"><?php _e('Name', 'leads-gallery'); ?></th>
                <th scope="col" class="column-primary"><?php _e('Email', 'leads-gallery'); ?></th>
                <th scope="col" class="column-primary"><?php _e('Created At', 'leads-gallery'); ?></th>
            </tr>
        </thead>
        <tbody>
            
        <?php
        foreach($rows as $row){
        ?>
            <tr>
                <td><?php echo $row->ds_name;?></td>
                <td><?php echo $row->ds_email;?></td>
                <td><?php echo date_format(date_create($row->dt_cadastro), 'd/m/Y h:m:s');?></td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
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
//	'wp_leads_list', 
//	array( 
//            'ds_name' => 'Jefferson Bessa', 
//            'ds_email' => 'jefferson.teixeira@uvv.br',
//            'dt_cadastro' => date('Y-m-d h:i:s')
//	), 
//	array( 
//            '%s', 
//            '%s' 
//	) 
//    );
//}