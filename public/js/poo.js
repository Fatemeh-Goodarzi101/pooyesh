if (document.body.classList.contains('logged-in')) {
 jQuery(document).ready(function(){
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
                complete: function( data ){
                  var r = confirm("امضای شما با موفقیت ثبت شد");
                  if (r == true) {
                      window.location.reload();
                  } else {
                      window.location.reload();
                  }
                }
            });
          });
 	});
} else {
  jQuery('.submit-digits').addClass('digits-login-modal').attr('type',1);
}

