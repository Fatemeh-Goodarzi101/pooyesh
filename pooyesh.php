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

if ( ! defined( 'WPINC' ) ) {
	die( 'No direct access.' );
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

		// Add custom post type and taxonomy: pooyesh
		add_action( 'init', array( $this, 'my_custom_post_type') );
		add_action( 'init', array( $this, 'my_custom_taxonomy' ) );

		// Plugin activation and deactivation
		register_activation_hook( __FILE__, array( $this, 'activate' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		add_action( 'after_switch_theme', array( $this, 'activate' ) );

		// Include CSS, JS admin file for Plugin
		add_action( 'admin_enqueue_scripts', array( $this, 'resources' ) );

		// Add custom fields to pooyesh post type
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_meta_box' ) );

		// Add the custom columns and data to the pooyesh post type
		add_filter( 'manage_pooyesh_posts_columns', array( $this, 'set_custom_pooyesh_columns' ) );
		add_action( 'manage_pooyesh_posts_custom_column' , array( $this, 'custom_pooyesh_column_data' ), 10, 2 );

	}

	// Include CSS, JS admin file for Plugin
	function resources() {
		wp_enqueue_style( 'bootstrap-css', plugin_dir_url( __FILE__ ) . 'assets/css/bootstrap.min.css' );
		wp_enqueue_script( 'bootstrap-js', plugin_dir_url( __FILE__ ) . 'assets/js/bootstrap.min.js' );
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
		);

		register_post_type( 'pooyesh', $args );
	}
    function my_custom_taxonomy() {
	    $labels = array(
		    'name' => _x( 'Pooyesh Categories', 'taxonomy general name' ),
		    'singular_name' => _x( 'Pooyesh Category', 'taxonomy singular name' ),
		    'search_items' => __( 'Search Pooyesh Categories' ),
		    'all_items' => __( 'All Pooyesh Categories' ),
		    'parent_item' => __( 'Parent Pooyesh Category' ),
		    'parent_item_colon' => __( 'Parent Pooyesh Category:' ),
		    'edit_item' => __( 'Edit Pooyesh Category' ),
		    'update_item' => __( 'Update Pooyesh Category' ),
		    'add_new_item' => __( 'Add New Pooyesh Category' ),
		    'new_item_name' => __( 'New Pooyesh Category' ),
		    'menu_name' => __( 'Categories' ),
	    );

	    $args = array(
		    'labels' => $labels,
		    'hierarchical' => true,
	    );

	    register_taxonomy( 'pooyesh_category', 'pooyesh', $args );
    }

	// Plugin activation and deactivation
	function activate() {
		$this->my_custom_post_type();

		// Flush permalinks
		flush_rewrite_rules();
	}
	function deactivate() {
		unregister_post_type( 'pooyesh' );
		flush_rewrite_rules();
	}

	// Add custom fields to pooyesh post type
	function add_meta_boxes() {
		add_meta_box(
			'pooyesh_meta_box',       // id
			__( 'Detail', 'pooyesh' ),       // name
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

		do_action( 'pooyesh_edit_start' );
		?>

		<table class="pooyesh-edit-table" role="presentation">
			<tbody>
                <tr>
                    <td class="col-lg-7">
                        <label for="_poo_support"><?php _e( 'Support', 'pooyesh' ); ?></label>
                        <input type="text" name="_poo_support" id="_poo_support" value="<?php echo $support; ?>">
                    </td>

                    <td class="col-lg-7">
                        <label for="_poo_hashtag"><?php _e( 'Hashtag', 'pooyesh' ); ?></label>
                        <input type="text" name="_poo_hashtag" id="_poo_hashtag" value="<?php echo $hashtag; ?>">
                    </td>
                    </div>
                </tr>
                <tr>
                    <td class="col-lg-7">
                        <label for="_poo_start_date"><?php _e( 'Start Date', 'pooyesh' ); ?></label>
                        <input type="date" name="_poo_start_date" id="_poo_start_date" value="<?php echo $start_date; ?>">
                    </td>
                    <td class="col-lg-7">
                        <label for="_poo_end_date"><?php _e( 'End Date', 'pooyesh' ); ?></label>
                        <input type="date" name="_poo_end_date" id="_poo_end_date" value="<?php echo $end_date; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="col-lg-7">
                        <label for="_poo_sign_count"><?php _e( 'Sign Count', 'pooyesh' ); ?></label>
                        <input type="text" name="_poo_sign_count" id="_poo_sign_count" value="<?php echo $sign_count; ?>">
                    </td>

                    <td class="col-lg-7">
                        <label for="_poo_status"><?php _e( 'Status', 'pooyesh' ); ?></label>
                        <input type="text" name="_poo_status" id="_poo_status" value="<?php echo $status; ?>">
                    </td>
                    </div>
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
            '_poo_sign_count'
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
}
Pooyesh::init();