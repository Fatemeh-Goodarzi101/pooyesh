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
                <div class="polaroid" style="border-top-left-radius: 10px; border-top-right-radius: 10px; ">
                    <div >
                        <?php the_post_thumbnail( 'full', array( 'class' => 'img-responsive' ) ); ?>
                    </div>
                  
				
                </div >
					<div style="text-align: left; background-color: white;">
						     <i class="fas fa-user-plus circle-icon" style="font-size:30px;color:rgba(255,184,47,255);"></i>
					</div>
				<div style=" box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);  border: 2px solid white;border-bottom-left-radius: 10px;border-bottom-right-radius:10px;">
                <div class="custom-container" >
                    <p style="text-align:center;font-size:16px;"><?php the_title(); ?></p>
                </div>
				 <div class="custom-container1" style="border-top: 5px solid #3eb7a4; padding-top:2px;">
                    <p style="padding-top:5px">از طرف:
                    <?php echo $support; ?>
                    </p>
                </div>
						</div>
            </a>
        </div>
	    <?php
	    endwhile;
    endif;
    ?>
    </div>

<?php get_footer(); ?>