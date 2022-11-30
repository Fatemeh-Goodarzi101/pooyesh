<?php
get_header(); ?>

<div class="container" style="text-align: center;">
    <img style="margin-top: 15px;" width="2560" height="931" src="https://sdil.ac.ir/wp-content/uploads/2022/11/4745103-scaled.jpg"
         class="attachment-full size-full" alt="" loading="lazy">

    <!-- Latest News Part  -->
    <h3>آخرین اخبار مرتبط با پویش‌ها:</h3><br>
    <?php
    global $post;
    $cat_posts = get_posts( array(
	    'posts_per_page' => 5,
	    'category'       => 3242
    ) );
    if ( $cat_posts ) { ?>
        <div class="card-group" style="display: flex">
            <?php
	        foreach ( $cat_posts as $post ) :
		    setup_postdata( $post );
		    $url = wp_get_attachment_url( get_post_thumbnail_id( get_the_ID() ), 'thumbnail' );
		    ?>
          	<a href="<?php the_permalink(); ?>">	
                <div class="card" style="margin: 5px;">
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

    <!-- Chosen Pooyesh Part  -->

    <h3>پویش های برگزیده</h3><br>
	<div class="row">
		<?php
		$args = array(
			'post_type' => 'pooyesh', // enter custom post type
			'orderby' => 'date',
			'order' => 'DESC',
			'meta_key'      => '_poo_chosen',
			'meta_value'    => 'yes'
		);

		$loop = new WP_Query( $args );
		if( $loop->have_posts() ):
			while( $loop->have_posts() ): $loop->the_post(); global $post; ?>
				<?php
				$count = get_post_meta(get_the_ID() , '_poo_sign_count' , true);
				?>
      			<?php
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
					<div style="background-color:lavender;" class="col-sm-3">
						<?php $url = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()), 'thumbnail' ); ?>
                        <a href="<?php the_permalink(); ?>">
						    <img src="<?php echo $url ?>" class="img-thumbnail" alt="<?php the_title(); ?>">
                        </a>
						<div>
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
						
                        <a href="<?php the_permalink(); ?>">
                            <div style="background-color:lavenderblush;margin-bottom:0px;height:30vh;user-select: none; " class="well">
                                <h3><?php the_title(); ?></h3>
                            </div>
                            <div style=" margin-bottom:5vh;">
                                <?php if(empty($count)){ ?>
                                <h4>تا کنون <span class="label label-info">7</span>
                                    نفر عضو این پویش شده اند</h4>
                                <?php } else { ?>
                                <h4>تا کنون <span class="label label-info"><?php echo $count; ?></span>
                                    نفر عضو این پویش شده اند</h4>
	                            <?php }
	                            ?>
                            </div>
                        </a>
					</div>
			<?php
			endwhile;
		endif;
		?>
	</div>

    <!-- My Pooyesh Part  -->

    <div class="row">
			<?php
			$user_id = get_current_user_id();
			global $wpdb;
			$table = $wpdb->prefix . 'pooyesh_user';
			$join_table = $wpdb->prefix . 'posts';
			$pooyesh = $wpdb->get_results(
				"SELECT pu.post_id , p.*
                 From $table AS pu
                 Inner Join $join_table AS p ON pu.post_id = p.ID
                 WHERE pu.user_id = $user_id" );
			?>
            <?php if($pooyesh) { ?>
                <h3>پویش های من</h3><br>
			    <?php foreach ($pooyesh as $item) { ?>
				<div style="background-color:lavender;" class="col-sm-3">
					<?php $url = wp_get_attachment_url( get_post_thumbnail_id($item->ID), 'thumbnail' ); ?>
					<img src="<?php echo $url ?>" class="img-thumbnail" alt="<?php echo $item->post_title; ?>">
					<div>
						<button type="button" class="btn btn-warning pull-right">عضو هستید
                            <span class="glyphicon glyphicon-ok-sign"></span>
                        </button>
					</div>
					<div style="background-color:lavenderblush;margin-bottom:0px;height:30vh;user-select: none;" class="well">
						<h3><?php echo $item->post_title; ?></h3>
					</div>
					<div style=" margin-bottom:5vh;">
						<?php
						$count = get_post_meta($item->ID , '_poo_sign_count' , true);
                        if(empty($count)){ ?>
                            <h4>تا کنون <span class="label label-info">7</span>
                                نفر عضو این پویش شده اند</h4>
                        <?php } else { ?>
                            <h4>تا کنون <span class="label label-info"><?php echo $count; ?></span>
                                نفر عضو این پویش شده اند</h4>
                        <?php }
						?>

					</div>
				</div>
			<?php } ?>
			<?php } else { ?>

	        <?php } ?>
		</div>

    <!-- All Pooyesh Part  -->
    <h3>همه پویش ها</h3><br>
    <div class="row">
		<?php
		$args = array(
			'post_type' => 'pooyesh', // enter custom post type
			'orderby' => 'date',
			'order' => 'DESC',
		);

		$loop = new WP_Query( $args );
		if( $loop->have_posts() ):
			while( $loop->have_posts() ): $loop->the_post(); global $post; ?>
				<?php
				$count = get_post_meta(get_the_ID() , '_poo_sign_count' , true);
				?>
      			<?php
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
                <div style="background-color:lavender;" class="col-sm-3">
					<?php $url = wp_get_attachment_url( get_post_thumbnail_id(get_the_ID()), 'thumbnail' ); ?>
                    <a href="<?php the_permalink(); ?>">
                        <img src="<?php echo $url ?>" class="img-thumbnail" alt="<?php the_title(); ?>">
                    </a>
                    <div>
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
                    <a href="<?php the_permalink(); ?>">
                        <div style="background-color:lavenderblush;margin-bottom:0px;height:30vh;user-select: none; " class="well">
                            <h3><?php the_title(); ?></h3>
                        </div>
                        <div style=" margin-bottom:5vh;">
                            <?php if(empty($count)){ ?>
                            <h4>تا کنون <span class="label label-info">7</span>
                                نفر عضو این پویش شده اند</h4>
                            <?php } else { ?>
                            <h4>تا کنون <span class="label label-info"><?php echo $count; ?></span>
                                نفر عضو این پویش شده اند</h4>
	                        <?php }
	                        ?>
                        </div>
                    </a>
                </div>
			<?php
			endwhile;
		endif;
		?>
    </div>
    <br>
</div>

<?php get_footer(); ?>

