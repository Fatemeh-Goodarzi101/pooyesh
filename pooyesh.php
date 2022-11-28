<?php
/**
 * Plugin Name: POOYESH
 * Plugin URI: pooyesh
 * Description: This plugin use for gathering signs of people in every events.
 * Version: 1.0.0
 * Author: Fatemeh Goodarzi
 * Author URI: https://fatemehgoodarzi.com/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Pooyesh {
	private static $instance = null;

	static function get_instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new Pooyesh();
		}

		return self::$instance;
	}

	static function init() {
		self::get_instance();
	}

	private function __construct() {

		// Add custom post type: pooyesh
		add_action( 'init', array( $this, 'my_custom_post_type') );

		// Plugin activation and deactivation
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_activation_hook( __FILE__, array( $this, 'my_custom_table' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		add_action( 'after_switch_theme', array( $this, 'activate' ) );

		// Include CSS, JS admin file for Plugin
		add_action( 'admin_enqueue_scripts', array( $this, 'resources' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'front_style' ) );
      	add_action( 'wp_enqueue_scripts', array( $this, 'ajax_code' ) );
		add_action('wp_head', array( $this, 'wpb_hook_javascript' ) );

		// Add custom fields to pooyesh post type
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );

		// Add the custom columns and data to the pooyesh post type
		add_filter( 'manage_pooyesh_posts_columns', array( $this, 'set_custom_pooyesh_columns' ) );
		add_action( 'manage_pooyesh_posts_custom_column' , array( $this, 'custom_pooyesh_column_data' ), 10, 2 );

        // Front pages of plugin
		add_filter( 'template_include',  array( $this, 'plugin_custom_template' ) );

        // Form submit sign
		add_action( 'wp_ajax_prefix_save_custom_form_data', array( $this, 'prefix_save_custom_form_data' ) );
		add_action( 'wp_ajax_nopriv_prefix_save_custom_form_data', array( $this, 'prefix_save_custom_form_data' ) );

        // Category based on post type => pooyesh
		add_action('save_post', array( $this, 'add_title_as_category') );

	}

	// Include CSS, JS admin file for Plugin
	function resources() {
		wp_enqueue_style( 'bootstrap-css', plugin_dir_url( __FILE__ ) . 'admin/css/bootstrap.min.css' );
		wp_enqueue_script( 'bootstrap-js', plugin_dir_url( __FILE__ ) . 'admin/js/bootstrap.min.js' );
	}

    function front_style() {
	    wp_enqueue_style( 'bootstrap-front-css', plugin_dir_url( __FILE__ ) . 'public/css/app.css' );
	    wp_enqueue_script( 'bootstrap-front-js', plugin_dir_url( __FILE__ ) . 'public/js/app.js' );
    }
	
  	function ajax_code($hook) {
      	wp_enqueue_script( 'pooyesh-js', plugin_dir_url( __FILE__ ) . 'public/js/poo.js' , array('jquery'),false,true );
      	$rest_nonce = wp_create_nonce( 'wp_rest' );
      	wp_localize_script( 'pooyesh-js', 'my_var', array( 'ajaxurl' => admin_url( 'admin-ajax.php'), 'nonce' => $rest_nonce, ));
    }
  
	function wpb_hook_javascript() {
    ?>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <?php
	}

	// Add custom post type and taxonomy: pooyesh
	function my_custom_post_type() {
		$labels = array(
			'name'               => 'Pooyesh', // General name for the post type.
			'menu_name'          => 'Pooyesh',
			'singular_name'      => 'Pooyesh',
			'all_items'          => 'Pooyesh',
			'search_items'       => 'Search Pooyeshs',
			'add_new'            => 'Add New',
			'add_new_item'       => 'Add New Pooyesh',
			'new_item'           => 'New Pooyesh',
			'view_item'          => 'View Pooyesh',
			'edit_item'          => 'Edit Pooyesh',
			'not_found'          => 'No Pooyeshs Found.',
			'not_found_in_trash' => 'Pooyesh not found in Trash.',
			'parent_item_colon'  => 'Parent Pooyesh',
		);

		$args = array(
			'labels'             => $labels,
			'description'        => 'Pooyesh',
			'menu_position'      => 5,
			'menu_icon'          => 'dashicons-media-video',
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'show_in_admin_bar'  => true,
			'query_var'          => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'thumbnail', 'editor' ),
			'taxonomies'         => array( 'category' ),
		);

		register_post_type( 'pooyesh', $args );
	}

    // Create new table in database
    function my_custom_table(){

	    global $wpdb;

	    $charset_collate = $wpdb->get_charset_collate();
	    $table = $wpdb->prefix . 'pooyesh_user';

	    $table_query = "CREATE TABLE $table(
            id int(11) NOT NULL AUTO_INCREMENT,
            user_id int(11) NOT NULL,
            post_id int(11) NOT NULL,
            date date NOT NULL,
            PRIMARY KEY id (id),
            FOREIGN KEY (user_id) REFERENCES ".$wpdb->prefix."users(id) ON DELETE CASCADE ON UPDATE CASCADE,
            FOREIGN KEY (post_id) REFERENCES ".$wpdb->prefix."posts(id) ON DELETE CASCADE ON UPDATE CASCADE,
            UNIQUE KEY unique_index (user_id,post_id)
        )$charset_collate;";

	    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	    dbDelta($table_query);
    }

	// Plugin activation and deactivation
	function activate() {
		$this->my_custom_table();
		$this->my_custom_post_type();
	}
	function deactivate() {
		unregister_post_type( 'pooyesh' );
		flush_rewrite_rules();
	}

	// Add custom fields to pooyesh post type
	function add_meta_boxes() {
		add_meta_box(
			'pooyesh_meta_box',       // id
			__( 'جزئیات پویش', 'pooyesh' ),       // name
			array( $this, 'display_meta_box_information' ),  // display function
			'pooyesh'                             // post type
		);

	}
	function display_meta_box_information( $post ) {
		wp_nonce_field( 'poo_my_nonce', 'poo_nonce_field' );

		$support = get_post_meta( $post->ID, '_poo_support', true );
		$hashtag = get_post_meta( $post->ID, '_poo_hashtag', true );
		$start_date = get_post_meta( $post->ID, '_poo_start_date', true );
		$end_date = get_post_meta( $post->ID, '_poo_end_date', true );
		$status = get_post_meta( $post->ID, '_poo_status', true );
		$sign_count = get_post_meta( $post->ID, '_poo_sign_count', true );
        $chosen = get_post_meta( $post->ID, '_poo_chosen' , true);
        $progress_bar = get_post_meta( $post->ID, '_poo_progress_bar' , true);

		do_action( 'pooyesh_edit_start' );
		?>

		<table class="pooyesh-edit-table" role="presentation">
			<tbody>
                <tr>
                    <td class="col-lg-8">
                        <label for="_poo_support"><?php _e( 'از طرف', 'pooyesh' ); ?></label>
                        <input style="margin: 5px;" type="text" name="_poo_support" id="_poo_support" value="<?php echo $support; ?>">
                    </td>

                    <td class="col-lg-8">
                        <label for="_poo_hashtag"><?php _e( 'هشتگ', 'pooyesh' ); ?></label>
                        <input type="text" name="_poo_hashtag" id="_poo_hashtag" value="<?php echo $hashtag; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="col-lg-8">
                        <label for="_poo_start_date"><?php _e( 'تاریخ شروع', 'pooyesh' ); ?></label>
                        <input style="margin: 5px;" type="date" name="_poo_start_date" id="_poo_start_date" value="<?php echo $start_date; ?>">
                    </td>
                    <td class="col-lg-8">
                        <label for="_poo_end_date"><?php _e( 'تاریخ پایان', 'pooyesh' ); ?></label>
                        <input type="date" name="_poo_end_date" id="_poo_end_date" value="<?php echo $end_date; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="col-lg-6">
                        <label for="_poo_sign_count"><?php _e( 'تعداد امضا', 'pooyesh' ); ?></label>
                        <input style="margin: 5px;" readonly type="text" name="_poo_sign_count" id="_poo_sign_count" value="<?php echo $sign_count; ?>">
                    </td>

                    <td class="col-lg-6">
                        <label for="_poo_progress_bar"><?php _e( 'درصد پیشرفت', 'pooyesh' ); ?></label>
                        <input type="text" name="_poo_progress_bar" id="_poo_progress_bar" value="<?php echo $progress_bar; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="col-lg-8">
                        <label style="margin: 5px;" for="_poo_status"><?php _e( 'وضعیت', 'pooyesh' ); ?></label><br>
                        <input type="radio" id="_poo_status_done" name="_poo_status" value="done" <?php if($status=='done'){ echo "checked=checked";}  ?>>
                        <label for="age1">به اتمام رسیده</label><br>
                        <input type="radio" id="_poo_status_doing" name="_poo_status" value="doing" <?php if($status=='doing'){ echo "checked=checked";}  ?>>
                        <label for="age2">در حال اجرا است</label><br>
                    </td>
                    <td class="col-lg-8">
                        <label style="margin: 5px;" for="_poo_sign_count"><?php _e( 'انتخاب به عنوان پویش برگزیده', 'pooyesh' ); ?></label><br>
                        <input type="radio" id="_poo_chosen_yes" name="_poo_chosen" value="yes" <?php if($chosen=='yes'){ echo "checked=checked";}  ?>>
                        <label for="age1">بله</label><br>
                        <input type="radio" id="_poo_chosen_no" name="_poo_chosen" value="no" <?php if($chosen=='no'){ echo "checked=checked";}  ?>>
                        <label for="age2">خیر</label><br>
                    </td>
                </tr>
			</tbody>
		</table>

		<?php
		do_action( 'pooyesh_edit_end' );
	}
	function save_meta_box( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
		if ( $parent_id = wp_is_post_revision( $post_id ) ) {
			$post_id = $parent_id;
		}
		$fields = [
			'_poo_support',
			'_poo_hashtag',
			'_poo_start_date',
            '_poo_end_date',
            '_poo_status',
            '_poo_sign_count',
            '_poo_chosen',
            '_poo_progress_bar'
		];
		foreach ( $fields as $field ) {
			if ( array_key_exists( $field, $_POST ) ) {
				update_post_meta( $post_id, $field, sanitize_text_field( $_POST[$field] ) );
			}
		}
	}

	// Add the custom columns and data to the pooyesh post type
	function set_custom_pooyesh_columns($columns) {
		$columns['support'] = __( 'Support', 'textdomain' );
		$columns['hashtag'] = __( 'Hashtag', 'textdomain' );
		$columns['start_date'] = __( 'Start Date', 'textdomain' );
		$columns['end_date'] = __( 'End Date', 'textdomain' );
		$columns['status'] = __( 'Status', 'textdomain' );
		$columns['sign_count'] = __( 'Sign Count', 'textdomain' );

		return $columns;
	}
	function custom_pooyesh_column_data( $column, $post_id ) {
		switch ( $column ) {
			case 'support' :
				echo get_post_meta( $post_id , '_poo_support' , true );
				break;

			case 'hashtag' :
				echo get_post_meta( $post_id , '_poo_hashtag' , true );
				break;

			case 'start_date' :
				echo get_post_meta( $post_id , '_poo_start_date' , true );
				break;

			case 'end_date' :
				echo get_post_meta( $post_id , '_poo_end_date' , true );
				break;

			case 'status' :
				echo get_post_meta( $post_id , '_poo_status' , true );
				break;

			case 'sign_count' :
				echo get_post_meta( $post_id , '_poo_sign_count' , true );
				break;
		}
	}

	// Front pages of plugin
	function plugin_custom_template($template) {

		$post_type = 'pooyesh';

		if ( is_post_type_archive( $post_type ) ){
            $template = plugin_dir_path( __FILE__ ) . "template/archive-$post_type.php";
		}

		if ( is_singular( $post_type ) ){
			$template = plugin_dir_path( __FILE__ ) . "template/single-$post_type.php";
		}
		return $template;
	}

    // Form submit sign
	function prefix_save_custom_form_data(){
        if ( wp_verify_nonce( $_POST['_wpnonce'], 'wp_rest' ) ){
          global $wpdb;
          global $current_user;
		  $user_id = $current_user->ID;
          if ($user_id != 0) {
            $insert_table = $wpdb->prefix . 'pooyesh_user';
            $insert_id = $wpdb->insert(
                $insert_table,
                array(
                    'user_id' => $user_id,
                    'post_id' => $_POST['post_id'],
                    'date' => date('Y-m-d', strtotime("now")),
                )
            );
            $sign_count = get_post_meta( $_POST['post_id'] , '_poo_sign_count' , true);
            $sign_count = $sign_count + 1;
            update_post_meta( $_POST['post_id'], '_poo_sign_count', $sign_count );
          }
          
        } else {
            echo 'nonce check failed';
            exit;
        }
	}

    // Category based on post type => pooyesh
	function add_title_as_category( $post_id ) {
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		$post = get_post($post_id);
		if ( $post->post_type == 'pooyesh') { // change 'post' to any cpt you want to target
			$term = get_term_by('slug', $post->post_name, 'category');
			if ( empty($term) ) {
				$add = wp_insert_term( $post->post_title, 'category', array('slug'=> $post->post_name) );
				if ( is_array($add) && isset($add['term_id']) ) {
					wp_set_object_terms($post_id, $add['term_id'], 'category', true );
				}
			} else {
				wp_update_term($term->term_id, 'category', array(
					'name' => $post->post_title
				));
			}
		}
	}


}
Pooyesh::init();