<?php
if(!defined('ABSPATH')){
    die(__('Access Denied', 'leads-gallery'));
}

# Posted by form
if(isset($_POST['e']) && $_POST['e'] == 'playlist'){
    
    $leads_gallery_error = leads_gallery_playlist_validation($_POST);
    
    if(empty($leads_gallery_error))
        $leads_gallery_message = leads_gallery_playlist_action($_POST);
}

if((isset($_GET['id']) && isset($_GET['action'])) && ($_GET['action'] == 'd')){
    
    leads_gallery_playlist_delete($_GET['id']);
    $leads_gallery_message = __('The playlist was deleted successfully!', 'leads-gallery');
}

elseif(isset($_GET['id'])){
    $leads_gallery_playlist = leads_gallery_playlist_recoverId($_GET['id']);
}

if(isset($_POST['e']) && $_POST['e'] == 'facebook'){
    
    $leads_gallery_error = leads_gallery_facebook_validation($_POST);
    
    if(empty($leads_gallery_error))
        $leads_gallery_message = leads_gallery_facebook_action($_POST);
}

$f = leads_gallery_facebook_recoverId();
if($f != false){
    $leads_gallery_facebook = $f;
}

function leads_gallery_playlist_action($post)
{
    global $leads_gallery_playlist;
    $playlist = leads_gallery_playlist_setObj($post);
    
    if($playlist->id != false)
    {
        leads_gallery_playlist_update($playlist);
        
        $message = __('Playlist updated successfully! Get the shortcode <strong>[video_leads_gallery id="%d"]</strong> to use. You can set style element for this shortcode, follow the example: <i>[video_leads_gallery id="%d" style="width: 100%"]</i>', 'leads-gallery');
        $message = str_replace('%d', $playlist->id, $message);
        
    }  else {
        
        leads_gallery_playlist_insert($playlist);
        #Recover after insert
        $leads_gallery_playlist = leads_gallery_playlist_recover();
        
        $message = __('Playlist saved successfully! Get the shortcode <strong>[video_leads_gallery id="%d"]</strong> to use. You can set style element for this shortcode, follow the example: <i>[video_leads_gallery id="%d" style="width: 100%"]</i>', 'leads-gallery');
        $message = str_replace('%d', $leads_gallery_playlist->id, $message);
    }
    
    return $message;
}

function leads_gallery_facebook_action($post)
{
    $recoveredConfig = leads_gallery_facebook_recoverId();
    $fb = leads_gallery_facebook_setObj($post);
    $fb->id = $recoveredConfig->id;
    
    if($fb->id != false)
    {
        leads_gallery_facebook_update($fb);
        
        $message = __('Facebook settings updated successfully!', 'leads-gallery');
        
    }  else {
        
        leads_gallery_facebook_insert($fb);
        
        $message = __('Facebook settings saved successfully!', 'leads-gallery');
    }
    
    return $message;
}

function leads_gallery_playlist_validation($post)
{
    if(empty($post['ds_embed']))
        return __('This settings cannot be saved, the <strong>Playlist Embed</strong> field is required', 'leads-gallery');
    
    if(empty($post['ds_width']))
        return __('This settings cannot be saved, the <strong>Width</strong> field is required', 'leads-gallery');
    
    if(empty($post['ds_height']))
        return __('This settings cannot be saved, the <strong>Height</strong> field is required', 'leads-gallery');
    
    return NULL;
}

function leads_gallery_facebook_validation($post)
{
    if(empty($post['id_facebook']))
        return __('This facebook settings cannot be saved, the <strong>Facebook App ID</strong> field is required', 'leads-gallery');
    
    return NULL;
}

function leads_gallery_playlist_setObj($post)
{
    $playlist = new stdClass();
    
    $playlist->id = isset($post['id']) ? addslashes($post['id']) : false;
    $playlist->ds_embed = base64_encode($post['ds_embed']);
    $playlist->ds_width = addslashes($post['ds_width']);
    $playlist->ds_height = addslashes($post['ds_height']);
    $playlist->fl_leads = isset($post['fl_leads']) ? addslashes($post['fl_leads']) : 0;
    
    return $playlist;
}

function leads_gallery_facebook_setObj($post)
{
    $fb = new stdClass();
    $fb->id_facebook = addslashes($post['id_facebook']);
    return $fb;
}

function leads_gallery_playlist_recover()
{
    global $wpdb;
    
    $rows = $wpdb->get_results( "SELECT * FROM wp_leads_playlists ORDER BY id DESC LIMIT 1" );
    
    foreach ($rows as $row)
    {
        $playlist = $row;
        break;
    }
    
    return $playlist;
}

function leads_gallery_playlist_recoverId($id)
{
    global $wpdb;
    
    $rows = $wpdb->get_results( "SELECT * FROM wp_leads_playlists WHERE id = " . $id . " ORDER BY id DESC" );
    
    foreach ($rows as $row)
    {
        $id = $row->id;
        $playlist = $row;
        break;
    }
    
    return isset($id) ? $playlist : false;
}

function leads_gallery_facebook_recoverId()
{
    global $wpdb;
    
    $rows = $wpdb->get_results( "SELECT * FROM wp_leads_facebook ORDER BY id DESC" );
    
    foreach ($rows as $row)
    {
        $id = $row->id;
        $fb = $row;
        break;
    }
    
    return isset($id) ? $fb : false;
}

function leads_gallery_playlist_insert($playlist)
{
    global $wpdb;
    
    $wpdb->insert( 
	'wp_leads_playlists', 
	array( 
            'ds_embed' => !empty($playlist->ds_embed) ? $playlist->ds_embed : NULL,
            'ds_width' => !empty($playlist->ds_width) ? $playlist->ds_width : NULL,
            'ds_height' => !empty($playlist->ds_height) ? $playlist->ds_height : NULL,
            'fl_leads' => !empty($playlist->fl_leads) ? $playlist->fl_leads : NULL,
            'dt_created' => date('Y-m-d h:i:s'),
	), 
	array( 
            '%s', 
            '%s',
            '%s',
            '%d',
            '%s',
	) 
    );
}

function leads_gallery_playlist_update($playlist)
{
    global $wpdb;
    
    $wpdb->update( 
        'wp_leads_playlists', 
        array( 
            'ds_embed' => !empty($playlist->ds_embed) ? $playlist->ds_embed : NULL,
            'ds_width' => !empty($playlist->ds_width) ? $playlist->ds_width : NULL,
            'ds_height' => !empty($playlist->ds_height) ? $playlist->ds_height : NULL,
            'fl_leads' => !empty($playlist->fl_leads) ? $playlist->fl_leads : NULL,
            'dt_created' => date('Y-m-d h:i:s'),
	), 
        array( 'ID' => $playlist->id ), 
        array( 
            '%s', 
            '%s',
            '%s',
            '%d',
            '%s',
	), 
        array( '%d' ) 
    );
}

function leads_gallery_playlist_delete($id)
{
    global $wpdb;
    $wpdb->delete( 'wp_leads_playlists', array( 'ID' => $id ) );
}

function leads_gallery_facebook_insert($fb)
{
    global $wpdb;
    
    $wpdb->insert( 
	'wp_leads_facebook', 
	array( 
            'id_facebook' => !empty($fb->id_facebook) ? $fb->id_facebook : NULL,
	), 
	array( 
            '%s',
	) 
    );
}

function leads_gallery_facebook_update($fb)
{
    global $wpdb;
    
    $wpdb->update( 
        'wp_leads_facebook', 
        array( 
            'id_facebook' => !empty($fb->id_facebook) ? $fb->id_facebook : NULL,
	), 
        array( 'ID' => $fb->id ), 
        array( 
            '%s',
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
