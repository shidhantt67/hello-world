<?php
add_action('wp_ajax_wqnew_entry', 'wqnew_entry_callback_function');
add_action('wp_ajax_nopriv_wqnew_entry', 'wqnew_entry_callback_function');

function wqnew_entry_callback_function() {
  global $wpdb;
  $wpdb->get_row( "SELECT * FROM `".$wpdb->prefix."affiliate_tbl` WHERE `name` = '".$_POST['wqtitle']."' AND `affiliate_key` = '".$_POST['wqdescription']."' AND `access_key` = '".$_POST['wqaccesskey']."' ORDER BY `id` DESC" );
  if($wpdb->num_rows < 1) {
    $wpdb->insert($wpdb->prefix."affiliate_tbl", array(
      "name" => $_POST['wqtitle'],
      "affiliate_key" => $_POST['wqdescription'],
	  "access_key" => $_POST['wqaccesskey'],
      "created_at" => time(),
      "updated_at" => time()
    ));

    $response = array('message'=>'Data Has Inserted Successfully', 'rescode'=>200);
    
	
  } else {
    $response = array('message'=>'Data Has Already Exist', 'rescode'=>404);
  }
  
  echo json_encode($response);
  exit();
  wp_die();
}



add_action('wp_ajax_wqedit_entry', 'wqedit_entry_callback_function');
add_action('wp_ajax_nopriv_wqedit_entry', 'wqedit_entry_callback_function');

function wqedit_entry_callback_function() {
  global $wpdb;
  $wpdb->get_row( "SELECT * FROM `".$wpdb->prefix."affiliate_tbl` WHERE `name` = '".$_POST['wqtitle']."' AND `affiliate_key` = '".$_POST['wqdescription']."' AND `access_key` = '".$_POST['wqaccesskey']."' AND `id`!='".$_POST['wqentryid']."' ORDER BY `id` DESC" );
  if($wpdb->num_rows < 1) {
    $wpdb->update( $wpdb->prefix."affiliate_tbl", array(
      "name" => $_POST['wqtitle'],
      "affiliate_key" => $_POST['wqdescription'],
	  "access_key" => $_POST['wqaccesskey'],
      "updated_at" => time()
    ), array('id' => $_POST['wqentryid']) );
    $response = array('message'=>'Data Has Updated Successfully', 'rescode'=>200);
  } else {
    $response = array('message'=>'Data Has Already Exist', 'rescode'=>404);
  }
  
  echo json_encode($response);
  exit();
  wp_die();
  // $wpdb->update( $wpdb->prefix."affiliate_tbl", array(
  //       "name" => $_POST['wqtitle'],
  //       "affiliate_key" => $_POST['wqdescription'],
  //     "access_key" => $_POST['wqaccesskey'],
  //       "updated_at" => time()
  //     ), array('id' => $_POST['wqentryid']) );
    
  //   wp_die();

}

add_action('wp_ajax_wq_delete', 'wqdelete_callback_function');
add_action('wp_ajax_nopriv_wq_delete', 'wqdelete_callback_function');

function wqdelete_callback_function() {
	global $wpdb;
	$wpdb->query("DELETE FROM `".$wpdb->prefix."affiliate_tbl` WHERE `id` = '".$_POST['id']."'" );
	wp_die();
	
}

add_action('wp_ajax_import_stores', 'import_stores_callback_function');
add_action('wp_ajax_nopriv_import_stores', 'import_stores_callback_function');

function import_stores_callback_function(){
  global $wpdb;
  $store = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."affiliate_tbl` WHERE `id` = '".$_POST['id']."'");
  $accesskey = $store->access_key;
  $affiliatekey = $store->affiliate_key;
  $response=array('acc' => $accesskey,'aff' => $affiliatekey);
  // $ar = return_cuelinks_offers($page = '' , $accesskey);
  //$ar = apply_filters('cuelinks_offers',$page = '' , $accesskey);
  echo json_encode($response);
  exit();
  wp_die();
}