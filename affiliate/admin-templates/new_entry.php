<?php
if(isset($_REQUEST['entryid']) && $_REQUEST['entryid']!='') {
  global $wpdb;
  $data = $wpdb->get_row( "SELECT * FROM `".$wpdb->prefix."affiliate_tbl` WHERE id = '".$_REQUEST['entryid']."'" );
?>
  <div class="wrap wqmain_body">
    <h3 class="wqpage_heading">Edit Affiliate</h3>
    <div class="wqform_body">
      <form name="update_form" id="update_form" >
        <input type="hidden" name="wqentryid" id="wqentryid" value="<?=$_REQUEST['entryid']?>" />
        <div class="wqlabel">Name</div>
        <div class="wqfield">
          <input type="text" class="wqtextfield" name="wqtitle" id="wqtitle" placeholder="Enter Affiliate name" value="<?=$data->name?>" />
        </div>
        <div id="wqtitle_message" class="wqmessage"></div>

        <div>&nbsp;</div>

        <div class="wqlabel">Affiliate Key</div>
        <div class="wqfield">
          <input type="text" name="wqdescription" class="wqtextfield" id="wqdescription" placeholder="Enter Affiliate key"value="<?=$data->affiliate_key?>"/>
        </div>
        <div id="wqdescription_message" class="wqmessage"></div>

        <div>&nbsp;</div>
		
		<div class="wqlabel">Access Key</div>
        <div class="wqfield">
          <input type="text" name="wqaccesskey" class="wqtextfield" id="accesskey" placeholder="Enter Access key" value="<?=$data->access_key?>"/>
        </div>
        <div id="wqaccess_message" class="wqmessage"></div>

        <div>&nbsp;</div>

        <div><input type="submit" class="wqsubmit_button" id="wqedit" value="Edit" /></div>
        <div>&nbsp;</div>
        <div class="wqsubmit_message"></div>

      </form>
    </div>
  </div>
<?php
} else {
?>
<div class="wrap wqmain_body">
  <h3 class="wqpage_heading">New Affiliate</h3>
  <div class="wqform_body">
    <form name="entry_form" id="entry_form" >

      <div class="wqlabel">Name</div>
      <div class="wqfield">
        <input type="text" class="wqtextfield" name="wqtitle" id="wqtitle" placeholder="Enter Affiliate" value="" />
      </div>
      <div id="wqtitle_message" class="wqmessage"></div>

      <div>&nbsp;</div>

      <div class="wqlabel">Affiliate Key</div>
      <div class="wqfield">
        <input type="text" name="wqdescription" class="wqtextfield" id="wqdescription" placeholder="Enter Affiliate key"/>
      </div>
      <div id="wqdescription_message" class="wqmessage"></div>

      <div>&nbsp;</div>
	  
	  <div class="wqlabel">Access Key</div>
      <div class="wqfield">
        <input type="text" name="wqaccesskey" class="wqtextfield" id="accesskey" placeholder="Enter Access key"/>
      </div>
      <div id="wqaccess_message" class="wqmessage"></div>

      <div>&nbsp;</div>



      <div><input type="submit" class="wqsubmit_button" id="wqadd" value="Add" /></div>
      <div>&nbsp;</div>
      <div class="wqsubmit_message"></div>

    </form>
  </div>
</div>
<?php } ?>
