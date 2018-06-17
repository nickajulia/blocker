<?php 

add_action('wp_ajax_save_plan_action', 'wcc_save_plan_action');
add_action('wp_ajax_nopriv_save_plan_action', 'wcc_save_plan_action');

function wcc_save_plan_action(){
	global $current_user, $wpdb;
	if( check_ajax_referer( 'save_plan_security_nonce', 'security') ){
		
		$table_name = 'calories_log';
		$table_name =  $wpdb->prefix.$table_name;
		$cur_date = $_POST['date'];
		$current_dishes = $wpdb->get_var($wpdb->prepare("SELECT meal FROM $table_name WHERE user = %s AND date = %s ",$current_user->ID, $cur_date ) );
		
		
		if( $_POST['save'] == '1' ){
			update_option('_save_plan_'.$current_user->ID, $current_dishes);
		}
		if( $_POST['save'] == '0' ){
			delete_option('_save_plan_'.$current_user->ID);
		}
		
	}
	die();
}



add_action('wp_ajax_save_recipe_action', 'wcc_save_recipe_action');
add_action('wp_ajax_nopriv_save_recipe_action', 'wcc_save_recipe_action');

function wcc_save_recipe_action(){
	global $current_user;
	if( check_ajax_referer( 'save_recipe_security_nonce', 'security') ){
		$all_user_recipes = (array)get_user_meta( $current_user->ID, 'user_saved_recipes', true );
		$all_user_recipes = array_unique($all_user_recipes);


		if( in_array( $_POST['save_id'], $all_user_recipes ) ){
			foreach( $all_user_recipes as $_val ){
				if( $_val != (int)$_POST['save_id'] ){
					$new_arr[] = $_val;
				}
			}

			update_user_meta( $current_user->ID, 'user_saved_recipes', $new_arr );
			echo 'removed';
		}else{
			$all_user_recipes[] = (int)$_POST['save_id'];
			update_user_meta( $current_user->ID, 'user_saved_recipes', $all_user_recipes );
			echo 'added';
		}
		
	}
	die();
}


add_action('wp_ajax_make_search_action', 'wcc_make_search_action');
add_action('wp_ajax_nopriv_make_search_action', 'wcc_make_search_action');

function wcc_make_search_action(){
	global $current_user;
	if( check_ajax_referer( 'search_security_nonce', 'security') ){
		
		update_user_meta( $current_user->ID, '_last_search', esc_html($_POST['s']) );
		
		if( strlen($_POST['s']) > 3 ){
			$search_list = get_option('_search_list');
			$search_list[] = $_POST['s'];
			
			$search_list = array_unique( $search_list );
			
			update_option('_search_list', $search_list);
		}
		
		
		$args = array(
			'showposts' => 10,
			'post_type' => 'food_product',
			's' => $_POST['s']
		);
		
		if( $_POST['type'] == 'reciepe' ){
			$args['meta_query'] = array(
					array(
						'key'     => 'food_type',
						'value'   => 'reciepe',

					),
				);
		}
		if( $_POST['type'] == 'product' ){
			$args['meta_query'] = array(
					array(
						'key'     => 'food_type',
						'value'   => 'product',

					),
				);
		}
		
		$args['meta_query'][] = 
					array(
						'key'     => 'is_private',
						'value'   => '1',
						'compare' => 'NOT EXISTS'
					);
		
		
	
		//var_Dump( $args );
		//var_dump( $_POST );
		$all_posts = get_posts( $args );
		if( count( $all_posts ) > 0 && $_POST['s'] != '' ){
			foreach( $all_posts as $single ){
				$out .= wcc_get_recipe( $single );
			}
			echo $out;
		}else{
			echo "<div class='span12'>".__('No results', 'wcc')."</div>";
		}
	}
	die();
}

add_action('wp_ajax_make_count_action', 'wcc_make_count_action');
add_action('wp_ajax_nopriv_make_count_action', 'wcc_make_count_action');

function wcc_make_count_action(){
	global $wpdb, $current_user;
	if( check_ajax_referer( 'count_security_nonce', 'security') ){

		$table_name = 'calories_log';
		$table_name =  $wpdb->prefix.$table_name;
		$cur_date = $_POST['date'];

		$current_dishes = $wpdb->get_var($wpdb->prepare("SELECT picked FROM $table_name WHERE user = %s AND date = %s ",$current_user->ID, $cur_date ) );


		//var_dump( $_POST );
		
		$dishes_unserialize = array();
		if( unserialize( $current_dishes ) ){
			$dishes_unserialize = unserialize( $current_dishes );
		}

		if( $_POST['sign_action'] == 'add' && !$_POST['portion_size'] ){
			$num_val =(int)$_POST['amount'];
		
			for($i = 0; $i < $num_val; $i++){
				$dishes_unserialize[] = $_POST['id'] ;
			}
			//$dishes_unserialize = array_unique( $dishes_unserialize );

			$wpdb->query($wpdb->prepare("UPDATE  $table_name SET  `picked` =  %s WHERE  `user` = %s AND `date` = %s ;", serialize($dishes_unserialize), $current_user->ID, $cur_date )) ;
		}
		if( $_POST['sign_action'] == 'add' && $_POST['portion_size'] ){
			$dish_id = (int)$_POST['id'];
			$dish_amount = (float)$_POST['portion_size'];
			
			$dishes_unserialize[] = array( $dish_id => $dish_amount );
			
			$wpdb->query($wpdb->prepare("UPDATE  $table_name SET  `picked` =  %s WHERE  `user` = %s AND `date` = %s ;", serialize($dishes_unserialize), $current_user->ID, $cur_date )) ;
		}
	
		if( $_POST['sign_action'] == 'remove' && $_POST['drob'] != '1' && !$_POST['flush']   ){
			$tmp_arr = array();
			$mark = 1;
			
			if( count($dishes_unserialize) > 0  )
				foreach( $dishes_unserialize as $s ){
					if( $s == $_POST['id'] /*&& $mark == 1*/ ){ $mark = 0; continue; }
					$tmp_arr[] = $s;
				}
			$dishes_unserialize = $tmp_arr;
			$wpdb->query($wpdb->prepare("UPDATE  $table_name SET  `picked` =  %s WHERE  `user` = %s AND `date` = %s ;", serialize($dishes_unserialize), $current_user->ID, $cur_date )) ;
		}
		if( $_POST['sign_action'] == 'remove' && $_POST['drob'] == '1'  ){
			
			//var_Dump( $dishes_unserialize );
			
			$tmp_arr = array();
			$mark = 0;
			if( count($dishes_unserialize) > 0  )
				foreach( $dishes_unserialize as $s ){
					
					//var_Dump( is_array($s) );
					
					if( is_array($s) ){
						$first_value = reset($s);
						$first_key = key($s);
						
						//var_dump( $first_value );
						//var_dump( $first_key );
						
						if( $first_key == (int)$_POST['id'] && $mark == 0 ){
							$mark = 1;
						}else{
							$tmp_arr[] = $s;
						}
					}else{
						$tmp_arr[] = $s;
					}
					
				
				}
			
			//var_Dump( $tmp_arr );			
				
			$dishes_unserialize = $tmp_arr;
			$wpdb->query($wpdb->prepare("UPDATE  $table_name SET  `picked` =  %s WHERE  `user` = %s AND `date` = %s ;", serialize($dishes_unserialize), $current_user->ID, $cur_date )) ;
		}
		//var_dump( $_POST['sign_action'] == 'remove' );
		//var_dump( $_POST['sign_action'] == 'remove' );
		//var_dump( $_POST['flush'] );
		if( $_POST['sign_action'] == 'remove'  && $_POST['drob'] != '1' && $_POST['flush']   ){
			//var_Dump( $dishes_unserialize );
			
			$tmp_arr = array();
			$mark = 0;
			if( count($dishes_unserialize) > 0  )
				foreach( $dishes_unserialize as $s ){
					
					//var_Dump( is_array($s) );
					
					if( is_array($s) ){
						$first_value = reset($s);
						$first_key = key($s);
						
						//var_dump( $first_value );
						//var_dump( $first_key );
						
						if( $first_key == (int)$_POST['id'] && $mark == 0 && $first_value == (float)$_POST['flush'] ){
							$mark = 1;
						}else{
							$tmp_arr[] = $s;
						}
					}else{
						$tmp_arr[] = $s;
					}
					
				
				}
			
			//var_Dump( $tmp_arr );			
				
			$dishes_unserialize = $tmp_arr;
			$wpdb->query($wpdb->prepare("UPDATE  $table_name SET  `picked` =  %s WHERE  `user` = %s AND `date` = %s ;", serialize($dishes_unserialize), $current_user->ID, $cur_date )) ;
			
			
		}
		
		echo wcc_get_progress_bar( $cur_date );
	}
	die();
}	


add_action('wp_ajax_spin_item_action', 'wcc_spin_item_action');
add_action('wp_ajax_nopriv_spin_item_action', 'wcc_spin_item_action');

function wcc_spin_item_action(){
	global $wpdb, $current_user;
	if( check_ajax_referer( 'spin_security_nonce', 'security') ){

		$table_name = 'calories_log';
		$table_name =  $wpdb->prefix.$table_name;
		$cur_date = $_POST['date'];

		
		
		
		$term_list = wp_get_post_terms($_POST['id'], 'food_category', array("fields" => "all"));
		$this_calories = wcc_get_calories( $_POST['id'] );
	
		$args = array(
			'showposts' => 100,
			'orderby' => 'rand',
			'post_type' => 'food_product',
			'tax_query' => array(
				array(
					'taxonomy' => 'food_category',
					'field'    => 'term_id',
					'terms'    => $term_list[0]->term_id,
				),
			),
			'meta_query' => array(
					array(
						'key'     => 'is_private',
						'value'   => '1',
						'compare' => 'NOT EXISTS'
					),
				),
		);
		
		if( get_user_meta( $current_user->ID, 'meat', true) == 'no'  ){
			$args['meta_query'][] = 
			array(
				'key'     => 'is_vegitarian',
				'value'   => 'yes',
			);
	
		}
		
		$all_posts = get_posts($args);
		
	 
		if( count($all_posts) > 0 )
		foreach( $all_posts as $single_post ){
			$post_cal = wcc_get_calories( $single_post->ID );
			if( $post_cal <= $this_calories + 15 && $post_cal > $this_calories - 15  ){
				$alt_found = 1;
				$replaace_id = $single_post->ID;
			}
		}
		
		if( $alt_found == 1 ){
			
			
			$current_dishes = $wpdb->get_var($wpdb->prepare("SELECT meal FROM $table_name WHERE user = %s AND date = %s ",$current_user->ID, $cur_date ) );
			
			//var_dump( $_POST['id'] );
			//var_dump( $replaace_id );
			//var_Dump( $current_dishes );
			$replaced_string =   str_replace(':'.$_POST['id'].';', ':'.$replaace_id.';', $current_dishes );
			$occurence_count = substr_count( $replaced_string, ':'.$replaace_id.';' );
			
		 
			//var_Dump( $replaced_string );
			
			
			$wpdb->query( $wpdb->prepare("UPDATE  $table_name SET  meal =  %s WHERE  user = %s AND date = %s ", $replaced_string, $current_user->ID, $cur_date  ) );
			echo wcc_get_recipe( get_post($replaace_id), null, false, $occurence_count );
			
		}else{
			echo 'error';
		}
		
	}
	die();
}


add_action('wp_ajax_spin_plan_item_action', 'wcc_spin_plan_item_action');
add_action('wp_ajax_nopriv_spin_plan_item_action', 'wcc_spin_plan_item_action');

function wcc_spin_plan_item_action(){
	global $wpdb, $current_user;
	if( check_ajax_referer( 'spin_plan_security_nonce', 'security') ){

		$this_id = $_POST['this_id'];
		$all_ids = explode(',', $_POST['all_ids'] );
		$original_id = $_POST['original_id'];
 
		$quantity = $_POST['quantity'];
		$current_index = array_search( $this_id,  $all_ids );
		if( $current_index == count($all_ids) - 1 ){
			$current_index = 0;
		}else{
			$current_index++;
		}
		
		$new_id = $all_ids[$current_index];

		echo wcc_get_meal_plan_dish( get_post( $new_id ), $quantity ,$original_id  );
	}
	die();
}


add_action('wp_ajax_load_more_action', 'wcc_load_more_action');
add_action('wp_ajax_nopriv_load_more_action', 'wcc_load_more_action');

function wcc_load_more_action(){
	global $wpdb, $current_user;
	if( check_ajax_referer( 'load_more_security_nonce', 'security') ){
		echo wcc_load_recipe_block( $_POST['count'], $_POST['type'] );
	}
	die();
}

?>