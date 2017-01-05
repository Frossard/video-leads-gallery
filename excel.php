<?php
if(!defined('ABSPATH')){
    die(__('Access Denied', 'leads-gallery'));
}

function export_csv()
{
    $csv = generate_csv();

    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private", false);
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"video_leads_gallery_list.csv\";");
    header("Content-Transfer-Encoding: binary");

    echo $csv;
    exit;
}

/**
* Converting data to CSV
*/
function generate_csv() 
{
    global $wpdb;
    
    $csv_output = __('Name', 'leads-gallery') . ';' . __('Email', 'leads-gallery') . ';' . __('Created At', 'leads-gallery') . ';';
   
    $values = $wpdb->get_results("SELECT * FROM wp_leads_list WHERE fl_active = '1' ORDER BY ds_name ASC");
   
    $csv_output .= "\n";
    
    foreach($values as $rowr){
       
        foreach($rowr as $key => $val)
        {
            if(in_array($key, array('ds_name', 'ds_email', 'dt_created')))
                $csv_output .= $val . ";";
        }
        $csv_output .= "\n";
    }
   
   return $csv_output;
}