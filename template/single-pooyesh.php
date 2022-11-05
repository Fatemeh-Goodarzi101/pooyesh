<?php

get_header(); ?>

    <?php
    while ( have_posts() ) :
        the_post();
        $hastag = get_post_meta( get_the_ID() , '_poo_hashtag' , true);
	    $support = get_post_meta( get_the_ID() , '_poo_support' , true);
	    $start_date = get_post_meta( get_the_ID() , '_poo_start_date' , true);
	    $end_date = get_post_meta( get_the_ID() , '_poo_end_date' , true);
	    $sign_count = get_post_meta( get_the_ID() , '_poo_sign_count' , true);
    ?>
    <div class="container">
        <div style="text-align: center;" class="mx-auto d-block">
	        <?php the_post_thumbnail(); ?>
        </div>
        <div style="text-align: center;padding: 5px;">
            #<?php echo $hastag; ?>
        </div>
        <h2 style="text-align: center; padding: 5px;">
	        <?php the_title(); ?>
        </h2>
        <p>
            <?php  the_content();  ?>
        </p>
        <div class="row">
            <div class="column">
                <b>.</b>
                پایان کارزار: <?php echo $end_date; ?>
            </div>
            <div class="column">
                <b>.</b>
                شروع کارزار: <?php echo $start_date; ?>
            </div>
        </div>
        <div class="row">
            <div class="column">
                <b>.</b>
                از طرف: <?php echo $support; ?>
            </div>
            <div class="column">
                <b>.</b>
                هشتگ رسمی: <?php echo $hastag; ?>
            </div>
        </div>
        <br>
        <div class="col-lg-12">
            <form class="custom-form-class" action="#" method="POST">

                <input type="hidden" name="action" value="sample_custom_form_action">
                <input style="display: none" type="text" name="post_id" value="<?php echo get_the_ID(); ?>">

                <label class="custom-input" for="first_name"><b>نام</b></label>
                <input class="custom-input" type="text" id="first_name" name="first_name" required>

                <label class="custom-input" for="last_name"><b>نام خانوادگی</b></label>
                <input class="custom-input" type="text" id="last_name" name="last_name" required>

                <label class="custom-input" for="phone"><b>شماره همراه</b></label>
                <input class="custom-input" type="tel" id="phone" name="phone" pattern="[0-9]{11}" required>

                <button class="custom-input" type="submit">ثبت امضا</button>
            </form>
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
