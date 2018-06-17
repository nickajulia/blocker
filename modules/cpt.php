<?php
if( !class_exists('vooCPT') ){
	class vooCPT{
		
		var $parameters;
		var $post_type;
		
		function __construct( $in_parameters, $post_type ){
			$this->parameters = $in_parameters;
			$this->post_type = $post_type;
		 
			add_action( 'init', array( $this, 'add_post_type' ), 1 );
			register_activation_hook( __FILE__, array( $this, 'add_post_type' ) );	 
			register_activation_hook( __FILE__, 'flush_rewrite_rules' );
		}
		function add_post_type(){
			register_post_type( $this->post_type, $this->parameters );
		}
 
	}
}


if( !class_exists('vooTax') ){
	class vooTax{
		
		var $parameters;
		var $post_type;
		var $tax_slug;
		
		function __construct( $tax_slug, $post_type, $in_parameters  ){
			$this->parameters = $in_parameters;
			$this->post_type = $post_type;
			$this->tax_slug = $tax_slug;
		 
			add_action( 'init', array( $this, 'register_taxonomy' ), 2  );
			register_activation_hook( __FILE__, array( $this, 'register_taxonomy' ) );	 
			//register_activation_hook( __FILE__, 'flush_rewrite_rules' );
		}
		function register_taxonomy(){
	
			register_taxonomy( $this->tax_slug, $this->post_type, $this->parameters );
		}
		 
	}
}


$labels = array(
    'name' => __('Lockers', $this->locale),
    'singular_name' => __('Locker', $this->locale),
    'add_new' => __('Add New', $this->locale),
    'add_new_item' => __('Add New Locker', $this->locale),
    'edit_item' => __('Edit Locker', $this->locale),
    'new_item' => __('New Locker', $this->locale),
    'all_items' => __('All Lockers', $this->locale),
    'view_item' => __('View Locker', $this->locale),
    'search_items' => __('Search Locker', $this->locale),
    'not_found' =>  __('No Lockers found', $this->locale),
    'not_found_in_trash' => __('No Lockers found in Trash', $this->locale), 
    'parent_item_colon' => '',
    'menu_name' => __('Lockers', $this->locale)

  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'show_in_menu' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'has_archive' => true, 
    'hierarchical' => false,
    'menu_position' => null,
    'supports' => array( 'title', 'editor' /*'editor' , 'thumbnail', 'excerpt', 'custom-fields'   'custom-fields' 'custom-fields'  'editor', 'thumbnail', 'custom-fields'  'author', , 'custom-fields', 'editor'  */)
  ); 

 
$new_pt = new vooCPT( $args, 'lockers' );


 $labels = array(
		'name'                       => _x( 'Category', $this->locale),
		'singular_name'              => _x( 'Category', $this->locale ),
		'search_items'               => __( 'Search Categories', $this->locale ),
		'popular_items'              => __( 'Popular Categories', $this->locale ),
		'all_items'                  => __( 'All Categories', $this->locale ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit Category', $this->locale ),
		'update_item'                => __( 'Update Category', $this->locale ),
		'add_new_item'               => __( 'Add New Category', $this->locale ),
		'new_item_name'              => __( 'New Category Name', $this->locale ),
		'separate_items_with_commas' => __( 'Separate Categories with commas', $this->locale ),
		'add_or_remove_items'        => __( 'Add or remove Categories', $this->locale ),
		'choose_from_most_used'      => __( 'Choose from the most used Categoryies', $this->locale ),
		'not_found'                  => __( 'No Categories found.', $this->locale ),
		'menu_name'                  => __( 'Category', $this->locale ),
	);

	$args = array(
		'hierarchical'          => true,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => true,
	);

 
	
//new vooTax( 'verse_cat', 'verse', $labels );
 


?>