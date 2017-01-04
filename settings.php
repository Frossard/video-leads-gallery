<?php
if(!defined('ABSPATH')){
    die(__('Access Denied', 'leads-gallery'));
}

/* Add a admin menu option */
function leads_gallery_add_new_menu_items()
{
    add_menu_page(
        "Video Leads Gallery",
        "Leads Gallery",
        "manage_options",
        "leads-gallery",
        "leads_gallery_page",
        '../wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/assets/img/icon-yt.png', //Optional. The URL to the menu item icon.
        20 //Optional. Position of the menu item in the menu.
    );
    
    add_submenu_page( 
        "leads-gallery", 
        __('Playlists', 'leads-gallery'). " Video Leads Gallery ", 
        __('Playlists', 'leads-gallery'), 
        "manage_options",
        "leads-gallery",
        "leads_gallery_page" 
    );
    
    add_submenu_page( 
        "leads-gallery", 
        __('Facebook Settings', 'leads-gallery'). " Video Leads Gallery ", 
        __('Facebook Settings', 'leads-gallery'), 
        "manage_options",
        "leads-gallery-facebook",
        "leads_gallery_page" 
    );
    
    add_submenu_page( 
        "leads-gallery", 
        __('List of Leads', 'leads-gallery'). " Video Leads Gallery ", 
        __('List of Leads', 'leads-gallery'), 
        "manage_options",
        "leads-gallery-list",
        "leads_gallery_page" 
    );
}

function leads_gallery_page()
{
    global $leads_gallery_facebook;
    global $leads_gallery_message;
    global $leads_gallery_error;
    ?>
    <div class="wrap">
        <div id="icon-options-general" class="icon32"></div>

        <h1>Video Leads Gallery</h1>
        
        <?php if(!empty($leads_gallery_message)): ?>
            <div id="message" class="updated notice notice-success is-dismissible">
                <p><?php echo $leads_gallery_message;?></p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text"><?php echo _e('Dismiss the notice', 'leads-gallery');?></span>
                </button>
            </div>
        <?php endif;?>
        
        <?php if(!empty($leads_gallery_error)): ?>
            <div id="message" class="error notice notice-error is-dismissible">
                <p><?php echo $leads_gallery_error;?></p>
                <button type="button" class="notice-dismiss">
                    <span class="screen-reader-text"><?php echo _e('Dismiss the notice', 'leads-gallery');?></span>
                </button>
            </div>
        <?php endif;?>

        <?php
            $active_tab = "leads-gallery";
            if(isset($_GET["page"]))
            {
                switch ($_GET["page"])
                {
                    case "leads-gallery":
                        $active_tab = "leads-gallery";
                        break;
                        
                    case "leads-gallery-facebook":
                        $active_tab = "leads-gallery-facebook";
                        break;
                        
                    case "leads-gallery-list":
                        $active_tab = "leads-gallery-list";
                        break;
                }
            }
        ?>

        <h2 class="nav-tab-wrapper">
            <a href="?page=leads-gallery" class="nav-tab <?php if($active_tab == 'leads-gallery'){echo 'nav-tab-active';} ?> "><?php _e('Playlists', 'leads-gallery'); ?></a>
            <a href="?page=leads-gallery-facebook" class="nav-tab <?php if($active_tab == 'leads-gallery-facebook'){echo 'nav-tab-active';} ?> "><?php _e('Facebook Settings', 'leads-gallery'); ?></a>
            <a href="?page=leads-gallery-list" class="nav-tab <?php if($active_tab == 'leads-gallery-list'){echo 'nav-tab-active';} ?>"><?php _e('List of Leads', 'leads-gallery'); ?></a>
        </h2>

        <?php 
        if(isset($_GET["page"]))
        { 
            switch ($_GET["page"])
            {
                case "leads-gallery":
                    
                    if(isset($_GET["action"]) && ($_GET["action"] == 'c' || (isset($_POST['e']) && $_POST['e'] == 'playlist'))){
                        ?>
                            <form method="post" action="">
                        <?php
                            settings_fields("playlist_section");
                            do_settings_sections("leads-gallery");

                            _e('(*) Required Fields', 'leads-gallery');
                        ?>
                                <p class="submit">
                                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Updates', 'leads-gallery'); ?>">
                                    <a href="?page=leads-gallery" class="button button-secondary" style="height: 29px;"><?php _e('Back', 'leads-gallery'); ?></a>
                                </p>
                            </form>  
                        <?php
                    }
                                        
                    else{
                        /* List */
                        settings_fields("list_section");
                        do_settings_sections("leads-gallery");
                        leads_gallery_display_playlists();                        
                    }
                    
                    break;

                case "leads-gallery-facebook":
                ?>
                    <form method="post" action="">
                <?php
                    settings_fields("leads_section");
                    do_settings_sections("leads-gallery");

                    _e('(*) Required Fields', 'leads-gallery');

                    submit_button();
                ?>
                    </form>  
                <?php
                    break;

                case "leads-gallery-list":
                    settings_fields("list_section");
                    do_settings_sections("leads-gallery");
                    leads_gallery_display_table();
                    break;
            }
        ?>
        <?php  
            }
        ?>     
    </div>
    <?php
}

add_action("admin_menu", "leads_gallery_add_new_menu_items");

function leads_gallery_display_options()
{
    if(isset($_GET["page"]))
    {
        switch ($_GET["page"])
        {
            case "leads-gallery":
                /* Playlist Youtube */
                
                if(isset($_GET["action"]) && ($_GET["action"] == 'c' || (isset($_POST['e']) && $_POST['e'] == 'playlist'))){
                    
                    add_settings_section("playlist_section", __('Youtube Playlist', 'leads-gallery'), "leads_gallery_display_playlist_description", "leads-gallery");

                    add_settings_field("ds_embed", __('Playlist Embed*', 'leads-gallery'), "leads_gallery_display_embed_form_element", "leads-gallery", "playlist_section");
                    register_setting("playlist_section", "ds_embed");

                    add_settings_field("ds_width", __('Width*', 'leads-gallery'), "leads_gallery_display_width_form_element", "leads-gallery", "playlist_section");
                    register_setting("playlist_section", "ds_width");

                    add_settings_field("ds_height", __('Height*', 'leads-gallery'), "leads_gallery_display_height_form_element", "leads-gallery", "playlist_section");
                    register_setting("playlist_section", "ds_height");

                    add_settings_field("fl_leads", __('Enable Leads Captation', 'leads-gallery'), "leads_gallery_display_lead_form_element", "leads-gallery", "playlist_section");
                    register_setting("playlist_section", "fl_leads");
                    
                }else{
                    
                    add_settings_section("list_section", __('Youtube Playlists', 'leads-gallery'), "leads_gallery_display_playlist_list_description", "leads-gallery");
                }
                break;
            
            case "leads-gallery-facebook":
                /* Captação de Leads */

                add_settings_section("leads_section", __('Captation settings', 'leads-gallery'), "leads_gallery_display_leads_description", "leads-gallery");

                add_settings_field("id_facebook", __('Facebook App ID*', 'leads-gallery'), "leads_gallery_display_id_form_element", "leads-gallery", "leads_section");
                register_setting("leads_section", "id_facebook");
                break;
            
            case "leads-gallery-list":
                add_settings_section("list_section", __('Registered Leads', 'leads-gallery'), "leads_gallery_display_list_description", "leads-gallery");
                break;
        }
                
    }
}

/* Tab 1 - Playlist Settings */

function leads_gallery_display_playlist_list_description(){
    echo "<p>" . __("This is the Youtube playlists registered, copy the shortcode to use.", 'leads-gallery') . "</p>";
}

function leads_gallery_display_playlist_description(){
    _e("First it's necessary set the Youtube playlist and it's vídeo width and height.", 'leads-gallery');
}

function leads_gallery_display_embed_form_element()
{
    global $leads_gallery_playlist;
    ?>
        <input type="hidden" name="e" value="playlist"/>
        <input type="hidden" name="id" value="<?php echo leads_gallery_getValue($leads_gallery_playlist, 'id'); ?>"/>
        <input type="text" name="ds_embed" id="ds_embed" style="width: 100%;" value='<?php echo leads_gallery_getValue_base64($leads_gallery_playlist, 'ds_embed'); ?>' />
        <i><xmp>Ex.: <iframe width="560" height="315" src="https://www.youtube.com/embed/r_vt3of4ukw?list=PLMVTEBBvR8MHZ2CKumJEIz3mnNeCnq2QO" frameborder="0" allowfullscreen></iframe></xmp></i>
    <?php
}

function leads_gallery_display_width_form_element()
{
    global $leads_gallery_playlist;
    ?>
        <input type="text" name="ds_width" id="ds_width" value="<?php echo leads_gallery_getValue($leads_gallery_playlist, 'ds_width'); ?>" placeholder="Ex.: 560px " /> <?php _e('(You can set 100% to)', 'leads-gallery');?>
    <?php
}

function leads_gallery_display_height_form_element()
{
    global $leads_gallery_playlist;
    ?>
        <input type="text" name="ds_height" id="ds_height" value="<?php echo leads_gallery_getValue($leads_gallery_playlist, 'ds_height'); ?>" placeholder="Ex.: 315px" /> <?php _e('(You can set 100% to)', 'leads-gallery');?>
    <?php
}

function leads_gallery_display_lead_form_element()
{
    global $leads_gallery_playlist;
    ?>
        <input type="checkbox" name="fl_leads" id="fl_leads" value="1" <?php echo (leads_gallery_getValue($leads_gallery_playlist, 'fl_leads') == 1) ? 'checked="checked"' : ''; ?>> <?php _e('Yes', 'leads-gallery');?>
    <?php
}

/* Tab 2 - Facebook Settings */

function leads_gallery_display_leads_description(){
    _e("After enabled leads captation, it's necessary set the Facebook ID for Facebook login app.", 'leads-gallery');
}

function leads_gallery_display_id_form_element()
{
    global $leads_gallery_facebook;
    ?>
        <input type="hidden" name="e" value="facebook"/>
        <input type="text" name="id_facebook" id="id_facebook" value="<?php echo leads_gallery_getValue($leads_gallery_facebook, 'id_facebook'); ?>" /> <?php _e('(Get on Facebook Login App)', 'leads-gallery');?>
    <?php
}

/* Tab 3 - Leads List */

function leads_gallery_display_list_description(){
    _e('This is the registered leads until today.', 'leads-gallery');
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
                <td><?php echo date_format(date_create($row->dt_created), 'd/m/Y h:m:s');?></td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
    <?php
}

function leads_gallery_display_playlists()
{
    global $wpdb;
    
    $rows = $wpdb->get_results( "SELECT * FROM wp_leads_playlists ORDER BY id DESC" );
    ?>
    <a href="?page=leads-gallery&action=c" class="button" style="background-color: #5bc0de; border-color: #46b8da; color: #fff; margin: 10px 0px 20px;"><?php _e('New Playlist', 'leads-gallery'); ?></a>
        
    <table class="wp-list-table widefat fixed striped posts" style="max-width:1000px;">
	<thead>
            <tr>
                <th scope="col" class="column-primary" width="30px">&nbsp;</th>
                <th scope="col" class="column-primary" width="30px">&nbsp;</th>
                <th scope="col" class="column-primary"><?php _e('Shortcode', 'leads-gallery'); ?></th>
                <th scope="col" class="column-primary"><?php _e('Lead Captation Status', 'leads-gallery'); ?></th>
            </tr>
        </thead>
        <tbody>
            
        <?php
        foreach($rows as $row){
        ?>
            <tr>
                <td>
                    <a href="?page=leads-gallery&action=d&id=<?php echo $row->id;?>" class="delete video-leads-gallery-icon" data-text="<?php _e('Are you sure about this exclusion?', 'leads-gallery');?>">
                        <img src="<?php echo '../wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/assets/img/icon-delete.png'; ?>" alt="<?php _e('Delete', 'leads-gallery'); ?>"/>
                    </a>
                </td>
                <td>
                    <a href="?page=leads-gallery&action=c&id=<?php echo $row->id;?>" class="video-leads-gallery-icon">
                        <img src="<?php echo '../wp-content/plugins/' . dirname(plugin_basename(__FILE__)) . '/assets/img/icon-edit.png'; ?>" alt="<?php _e('Edit', 'leads-gallery'); ?>"/>
                    </a>
                </td>
                <td>
                    [video_leads_gallery id="<?php echo $row->id;?>"]
                </td>
                <td>
                    <a href="?page=leads-gallery&action=c&id=<?php echo $row->id;?>">
                        <?php echo ($row->fl_leads == 1) ? __('Enabled', 'leads-gallery') : __('Disabled', 'leads-gallery');?>
                    </a>
                </td>
            </tr>
        <?php
        }
        ?>
        </tbody>
    </table>
    <?php
}

add_action("admin_init", "leads_gallery_display_options");