<?php

get_header(); ?>

    <?php
    while ( have_posts() ) :
        the_post();
        $progress_bar = get_post_meta( get_the_ID() , '_poo_progress_bar' , true);
	    $sign_count = get_post_meta( get_the_ID() , '_poo_sign_count' , true);
	    $url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' );

		$hastag = get_post_meta( get_the_ID() , '_poo_hashtag' , true);
	    $support = get_post_meta( get_the_ID() , '_poo_support' , true);
	    $start_date = get_post_meta( get_the_ID() , '_poo_start_date' , true);
	    $end_date = get_post_meta( get_the_ID() , '_poo_end_date' , true);

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

		$cat_detail = get_the_category($post_id);//$post->ID
		$cat_id = $cat_detail[0]->term_id;
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
                    <button id="<?php echo $post_id; ?>" type="button" class="btn btn-success pull-right submit-digits">
                        عضو
                        میشوم
                        <span class="glyphicon glyphicon-plus-sign"></span>
                    </button>
                <?php } else { ?>
	                <div>
                        <button type="button" class="btn btn-warning pull-right">عضو هستید
                           <span class="glyphicon glyphicon-ok-sign"></span>
                        </button>
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
				
              <div class="row">
                <div class="column">
                    <b>.</b>
                    پایان پویش: <?php echo $end_date; ?>
                </div>
                <div class="column">
                    <b>.</b>
                    شروع پویش: <?php echo $start_date; ?>
                </div>
            </div>
            <div class="row">
                <div class="column">
                    <b>.</b>
                    از طرف: <?php echo $support; ?>
                </div>
                <div class="column">
                    <b>.</b>
                    هشتگ رسمی: #<?php echo $hastag; ?>
                </div>
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
    <?php endwhile; // End of the loop. ?>
	
 <div class="container">
	<!-- Latest News Part  -->
    <h3>اخبار مرتبط با پویش‌:</h3><br>
    <?php
    global $post;
    $cat_posts = get_posts( array(
	    'posts_per_page' => 10,
	    'category'       => $cat_id
    ) );
    if ( $cat_posts ) { ?>
        <div class="card-group" style="display: flex">
            <?php
	        foreach ( $cat_posts as $post ) :
		    setup_postdata( $post );
		    $url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' );
		    ?>
          	<a href="<?php the_permalink(); ?>">	
                <div class="card" style="width: 18rem; margin: 5px;">
                    <img style=" width: 300px; height: 250px;" src="<?php echo $url ?>" class="card-img-top" alt="<?php the_title(); ?>">
                    <div class="card-body">
                        <p class="card-title"><?php the_title(); ?></p>
                    </div>
                </div>
          </a>
	    <?php
	    endforeach;
	    wp_reset_postdata(); ?>
        </div>
    <?php }
    ?>
</div>
<?php get_footer(); ?>
