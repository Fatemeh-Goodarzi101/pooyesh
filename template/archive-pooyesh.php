<?php
get_header(); ?>
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
		    $support = get_post_meta(get_the_ID() , '_poo_support' , true);
        ?>
        <div class="column">
            <a href="<?php the_permalink(); ?>">
                <div class="polaroid">
                    <div style="width:50%; border-radius: 10px; float: right; padding:10px;">
                        <?php the_post_thumbnail( 'full', array( 'class' => 'img-responsive' ) ); ?>
                    </div>
                    <div>
                        <p style="text-align:center; padding-top:50px;color: white;" > <?php echo $count; ?> نفرامضا کرده اند </p>
                        <img src="<?php echo get_stylesheet_directory_uri(); ?>/public/img/sign.jpg"  style="padding-top:50px;float:left" >

                    </div>
                </div>
                <div class="custom-container">
                    <p><?php the_title(); ?></p>
                    <p>از طرف:
                    <?php echo $support; ?>
                    </p>
                </div>
            </a>
        </div>
	    <?php
	    endwhile;
    endif;
    ?>
    </div>

<?php get_footer(); ?>