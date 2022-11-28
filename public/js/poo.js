jQuery(document).ready(function(){
  
  	jQuery(document).ready(function(){
		jQuery('.submit-digits').addClass('digits-login-modal').attr('type',1);
	});
  
	jQuery(".submit-digits").click(function(){
		var post_id = jQuery(this).attr("id");
      	
		$.ajax({
            method: 'POST', // Adding Post method
            dataType: 'json',
            url: my_var.ajaxurl, // Including ajax file
            data: {
				post_id : post_id,
				_wpnonce : my_var.nonce,
				action : 'prefix_save_custom_form_data'
			},
          	success: function( data ){
              console.log('امضای شما با موفقیت ثبت شد');
            }
        });
   });
});