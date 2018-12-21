<?php
/*
Plugin Name: Affiliates
Description: A Plugin for Affiliates
Author: SiD
Version: 1.0.0
*/

global $wpdb;
define('PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('PLUGIN_PATH', plugin_dir_path( __FILE__ ));

register_activation_hook( __FILE__, 'activate_plugin_function' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_function' );

function activate_plugin_function() {
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $table_name = $wpdb->prefix.'affiliate_tbl';

  $sql = "CREATE TABLE $table_name (
    `id` bigint(11) unsigned NOT NULL AUTO_INCREMENT,
    `name` varchar(255),
    `affiliate_key` varchar(255),
	`access_key` varchar(255),
    `created_at` varchar(255),
    `updated_at` varchar(255),
    PRIMARY KEY  (id)
  ) $charset_collate;";

  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  dbDelta( $sql );
}

function deactivate_plugin_function() {
  global $wpdb;
  $table_name = $wpdb->prefix.'affiliate_tbl';
  $sql = "DROP TABLE IF EXISTS $table_name";
  $wpdb->query($sql);
}

function load_custom_css_js() {
  wp_register_style( 'my_custom_css', PLUGIN_URL.'/css/style.css', false, '1.0.0' );
  wp_enqueue_style( 'my_custom_css' );
  wp_enqueue_script( 'my_custom_script1', PLUGIN_URL. '/js/custom.js' );
  wp_enqueue_script( 'my_custom_script2', PLUGIN_URL. '/js/jQuery.min.js' );
  wp_localize_script( 'my_custom_script1', 'ajax_var', array( 'ajaxurl' => admin_url('admin-ajax.php') ));
}
add_action( 'admin_enqueue_scripts', 'load_custom_css_js' );

require_once(PLUGIN_PATH.'/ajax/ajax_action.php');

add_action('admin_menu', 'my_menu_pages');
function my_menu_pages(){
    add_menu_page('Affiliate', 'Affiliate', 'manage_options', 'new-entry', 'my_menu_output' );
    add_menu_page('Import Stores','Import Stores','manage_options','import-stores','import_stores');
    add_menu_page('Import Coupons','Import Coupons','manage_options','import-coupons','import_coupons');
    add_submenu_page('new-entry', 'Affiliate', 'Create Affiliate', 'manage_options', 'new-entry', 'my_menu_output' );
    add_submenu_page('new-entry', 'Affiliate', 'View Affiliates', 'manage_options', 'view-entries', 'my_submenu_output' );
}
if (!class_exists('WP_List_Table')) {
  require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

function import_coupons() {

}
class StoresList extends WP_List_Table {
  function __construct() {
    global $status, $page;
    parent::__construct(array(
      'singular' => 'Store Data',
      'plural' => 'Store Datas'
    ));
  }
  
  function column_default($item, $column_name) {
    switch($column_name){
      case 'action': echo '<button href="" id="import_val" value="'.$item['id'].'">Import</button>';
    }
    return $item[$column_name];
  }
  function column_cb($item) {
    return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id'] );
  }
  function get_columns(){
    $columns = array(
      'cb' => '<input type="checbox" />',
      'name' => 'Name',
      'action' => 'Action'
    );
    return $columns;
  }
  function get_sortable_columns() {
    $sortable_columns = array(
      'name' => array('name', true)
    );
    return $srtable_columns;
  }

  function prepare_items(){
    global $wpdb,$current_user;
    $table_name = $wpdb->prefix."affiliate_tbl";
    $per_page = 10;
    $columns = $this->get_columns();
    $hidden = array();
    $sortable = $this->get_sortable_columns();
    $this->_column_headers = array($columns, $hidden, $sortable);
    $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

    $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
    $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
    $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';

    if(isset($_REQUEST['s']) && $_REQUEST['s']!='') {
      $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `name` LIKE '%".$_REQUEST['s']."%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged * $per_page), ARRAY_A);
    } else {
      $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged * $per_page), ARRAY_A);
    }

    $this->set_pagination_args(array(
      'total_items' => $total_items,
      'per_page' => $per_page,
      'total_pages' => ceil($total_items / $per_page)
    ));

  }
  
}

function import_stores(){
//require_once(PLUGIN_PATH.'/admin-templates/import_store.php');
global $wpdb;
$tbl = new StoresList();
$tbl->prepare_items();
ob_start();
?>
  <div class="wrap wqmain_body">
    <h3>Stores</h3>

    <form id="entry-table" method="GET">
      <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
      <?php $tbl->search_box( 'search', 'search_id' ); $tbl->display() ?>
    </form>
  </div>
<?php
  $wq_msg = ob_get_clean();
  echo $wq_msg;
}

function my_menu_output() {
  require_once(PLUGIN_PATH.'/admin-templates/new_entry.php');
  
}



class EntryListTable extends WP_List_Table {

    function __construct() {
      global $status, $page;
      parent::__construct(array(
        'singular' => 'Entry Data',
        'plural' => 'Entry Datas',
      ));
    }

    function column_default($item, $column_name) {
        switch($column_name){
          case 'action': echo '<a href="'.admin_url('admin.php?page=new-entry&entryid='.$item['id']).'"><button>Edit</buton></a>';
		  
		   echo '<button href="" id="delete_val" value="'.$item['id'].'">Delete</button>';
        }
        return $item[$column_name];
    }
    
    function column_feedback_name($item) {
      $actions = array( 'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id']) );
      return sprintf('%s %s', $item['id'], $this->row_actions($actions) );
    }

    function column_cb($item) {
      return sprintf( '<input type="checkbox" name="id[]" value="%s" />', $item['id'] );
    }

    function get_columns() {
      $columns = array(
        'cb' => '<input type="checkbox" />',
			  'name'=> 'Name',
        'affiliate_key'=> 'Affiliate key',
		'access_key'=> 'Access key',
        'action' => 'Action'
      );
      return $columns;
    }

    function get_sortable_columns() {
      $sortable_columns = array(
        'name' => array('name', true)
      );
      return $sortable_columns;
    }

    function get_bulk_actions() {
      $actions = array( 'delete' => 'Delete' );
      return $actions;
    }

    function process_bulk_action() {
      global $wpdb;
      $table_name = $wpdb->prefix."affiliate_tbl";
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
            }
        }
    }

    function prepare_items() {
      global $wpdb,$current_user;

      $table_name = $wpdb->prefix."affiliate_tbl";
		  $per_page = 10;
      $columns = $this->get_columns();
      $hidden = array();
      $sortable = $this->get_sortable_columns();
      $this->_column_headers = array($columns, $hidden, $sortable);
      $this->process_bulk_action();
      $total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

      $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
      $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
      $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'desc';

		  if(isset($_REQUEST['s']) && $_REQUEST['s']!='') {
        $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name WHERE `name` LIKE '%".$_REQUEST['s']."%' ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged * $per_page), ARRAY_A);
		  } else {
			  $this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged * $per_page), ARRAY_A);
		  }

      $this->set_pagination_args(array(
        'total_items' => $total_items,
        'per_page' => $per_page,
        'total_pages' => ceil($total_items / $per_page)
      ));
    }
}

function my_submenu_output() {
  global $wpdb;
  $table = new EntryListTable();
  $table->prepare_items();
  $message = '';
  if ('delete' === $table->current_action()) {
    $message = '<div class="div_message" id="message"><p>' . sprintf('Items deleted: %d', count($_REQUEST['id'])) . '</p></div>';
  }
  ob_start();
?>
  <div class="wrap wqmain_body">
    <h3>View Entries</h3>
    <?php echo $message; ?>
    <form id="entry-table" method="GET">
      <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
      <?php $table->search_box( 'search', 'search_id' ); $table->display() ?>
    </form>
  </div>
<?php
  $wq_msg = ob_get_clean();
  echo $wq_msg;
}
