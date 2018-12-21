jQuery(document).ready(function(){

  $(document).on('submit','#entry_form', function(e) {
    e.preventDefault();
    $('.wqmessage').html('');
    $('.wqsubmit_message').html('');

    var wqtitle = $('#wqtitle').val();
    var wqdescription = $('#wqdescription').val();
	var wqaccess = $('#accesskey').val();

    if(wqtitle=='') {
      $('#wqtitle_message').html('Name is Required');
    }
    if(wqdescription=='') {
      $('#wqdescription_message').html('Affiliation key is Required');
    }
	if(wqaccess=='') {
      $('#wqaccess_message').html('Access key is Required');
    }

    if(wqtitle!='' && wqdescription!='' && wqaccess!='') {
      var fd = new FormData(this);
      var action = 'wqnew_entry';
      fd.append("action", action);

      $.ajax({
        data: fd,
        type: 'POST',
        url: ajax_var.ajaxurl,
        contentType: false,
			  cache: false,
        processData:false,
        // success: function() {
         
        // window.location.replace("<?=  site_url(); ?>");
        // }
        success: function(response) {
          var res = JSON.parse(response);
          $('.wqsubmit_message').html(res.message);
          if(res.rescode!='404') {
            $('#entry_form')[0].reset();
            $('.wqsubmit_message').css('color','green');
          } else {
            $('.wqsubmit_message').css('color','red');
          }
        }
      });
    }
  });

  $(document).on('submit','#update_form', function(e) {
    e.preventDefault();
    $('.wqmessage').html('');
    $('.wqsubmit_message').html('');

    var wqtitle = $('#wqtitle').val();
    var wqdescription = $('#wqdescription').val();
	var wqaccess = $('#accesskey').val();

    if(wqtitle=='') {
      $('#wqtitle_message').html('Name is Required');
    }
    if(wqdescription=='') {
      $('#wqdescription_message').html('Affiliation key is Required');
    }
	if(wqaccess=='') {
      $('#wqaccess_message').html('Access key is Required');
    }

    if(wqtitle!='' && wqdescription!='' && wqaccess!='') {
      var fd = new FormData(this);
      var action = 'wqedit_entry';
      fd.append("action", action);

      $.ajax({
        data: fd,
        type: 'POST',
        url: ajax_var.ajaxurl,
        contentType: false,
			  cache: false,
			  processData:false,
        // success: function() {
			  // window.location("<?= echo site_url(); ?>");
        // }
        success: function(response) {
          var res = JSON.parse(response);
          $('.wqsubmit_message').html(res.message);
          if(res.rescode!='404') {
            $('#update_form')[0].reset();
            $('.wqsubmit_message').css('color','green');
          } else {
            $('.wqsubmit_message').css('color','red');
          }
        }
      });
    }
  });

$(document).on('click','#delete_val',function(){
	
	var resp = window.confirm('You want to delete ?');
	
	if(resp){
			var id = $('#delete_val').val();
		$.ajax({
			data : {action : 'wq_delete',
					id : id},
			type : 'post',
			url : ajax_var.ajaxurl,
			success : function(){
				
			}
			
		});
	}
});




$(document).on('click','#import_val',function(){
  var id = $('#import_val').val();
	 resp = window.confirm('You want to import ?');
	
	if(resp){
   var id = $('#import_val').val();
		 $.ajax({
		 	data : {action : 'import_stores',
		 			id : id},
		 	type : 'post',
		 	url : ajax_var.ajaxurl,
		 	success : function(response){
        var res = JSON.parse(response);
        alert(res.acc);
        $.each(res,function(key,value){
          console.log(key + ":" + value)
        });
        
		 	}
			
		 });
	 }
});


});
