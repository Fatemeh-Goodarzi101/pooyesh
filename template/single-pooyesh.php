<?php

get_header(); ?>

    <?php
    while ( have_posts() ) :
        the_post();
        $progress_bar = get_post_meta( get_the_ID() , '_poo_progress_bar' , true);
	    $sign_count = get_post_meta( get_the_ID() , '_poo_sign_count' , true);
	    $url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' );

	    $user_id = get_current_user_id();
        $post_id = get_the_ID();
	    global $wpdb;
	    $table      = $wpdb->prefix . 'pooyesh_user';
	    $join_table = $wpdb->prefix . 'posts';
	    $result    = $wpdb->get_results(
		    "SELECT pu.post_id , p.*
             From $table AS pu
             Inner Join $join_table AS p ON pu.post_id = p.ID
             WHERE pu.user_id = $user_id And pu.post_id = $post_id" );
	    ?>
        <div class="container">
            <div class="row">
                <div class="col-sm-12">
                    <div style="background-image: url(<?php echo $url; ?>); background-repeat: no-repeat; background-size:cover; height: 300px;">
                        <h3 style="padding: 5px;"><?php the_title(); ?></h3>
                        <div style=" margin:10px;">
	                        <?php if(empty($sign_count)){ ?>
                                <h6>تا کنون <span class="label label-info">7</span>
                                    نفر عضو این پویش شده اند</h6>
	                        <?php } else { ?>
                                <h6>تا کنون <span class="label label-info"><?php echo $sign_count; ?></span>
                                    نفر عضو این پویش شده اند</h6>
	                        <?php }
	                        ?>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-sm-12" style=" margin:10px;">
                <?php
                if(empty($result)) { ?>
                    <button type="button" class="btn btn-success pull-right" data-toggle="modal" data-target="#myModal">
                        عضو
                        میشوم
                        <span class="glyphicon glyphicon-plus-sign"></span>
                    </button>
                <?php } else { ?>
	                <div>
                        <h5 style="display: inline-block;">عضو هستید</h5>
                        <span class="glyphicon glyphicon-ok-sign"></span>
                    </div>
                <?php }
                ?>
            </div>

            <div class="col-sm-12" style=" margin-bottom:5vh;">
                <div class="text-center" style="user-select: none;margin-bottom:5vh">
                    <p>
	                    <?php  the_content();  ?>
                    </p>
                </div>

                <div>
                    <h4>پیشرفت پویش</h4>
                    <div class="progress">
                        <div class="progress-bar progress-bar-info" role="progressbar"
                             aria-valuenow="<?php echo $progress_bar; ?>" aria-valuemin="0" aria-valuemax="100" style="width:<?php echo $progress_bar; ?>%">
	                        <?php echo $progress_bar; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <script>
        // Javascript to send ajax request
        jQuery(document).on('submit', '.custom-form-class', function(e){
            let formData = jQuery(this).serialize();

            // Change ajax url value to your domain
            let ajaxurl = 'http://192.168.10.50/servertest/wp-admin/admin-ajax.php';

            // Send ajax
            jQuery.post(ajaxurl, formData, function(response) {
                alert('امضای شما با موفقیت ثبت شد: ' + response);
            });
        });
    </script>
    <?php endwhile; // End of the loop. ?>

<?php get_footer(); ?>
