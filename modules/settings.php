<?php 

if( !class_exists('vooSettingsClass') ){
class vooSettingsClass{
	
	var $setttings_parameters;
	var $settings_prefix;
	
	function __construct( $parameters, $prefix ){
		
		$this->setttings_parameters = $parameters;		
		$this->setttings_prefix = $prefix;		
		add_action('admin_menu', array( $this, 'add_menu_item') );
		add_action('init', array( $this, 'process_form_parameters') );

	}
	
	function process_form_parameters(){
		if(  wp_verify_nonce($_POST['save_settings_field'], 'save_settings_action') ){
			$options = array();
			foreach( $_POST as $key=>$value ){
				$options[$key] = sanitize_text_field( $value );
			}
			update_option( $this->setttings_prefix.'_options', $options );
		}
	}
	
	
	function add_menu_item(){
		
		foreach( $this->setttings_parameters as $single_option ){
			
			if( $single_option['type'] == 'submenu' ){
				add_submenu_page(  
				$single_option['parent_slug'],  
				$single_option['page_title'], 
				$single_option['menu_title'], 
				$single_option['capability'], 
				$single_option['menu_slug'], 
				array( $this, 'show_settings' ) 
				);
			}
			if( $single_option['type'] == 'option' ){
				add_option_page(  				  
				$single_option['page_title'], 
				$single_option['menu_title'], 
				$single_option['capability'], 
				$single_option['menu_slug'], 
				array( $this, 'show_settings' ) 
				);
			}
		}
		 
	}
	
	function show_settings(){
		?>
		<div class="wrap tw-bs">
		<h2><?php _e('Settings', 'sc'); ?></h2>
		<hr/>
		<form class="form-horizontal" method="post" action="">
		<?php 
		wp_nonce_field( 'save_settings_action', 'save_settings_field'  );  
		$config = get_option( $this->setttings_prefix.'_options'); 
		?>  
		<fieldset>

			<?php 
		foreach( $this->setttings_parameters as $single_page ){	
			foreach( $single_page['parameters'] as $key=>$value ){
				switch( $value['type'] ){
					case "separator":
						$out .= '
						<div class="lead">'.$value['title'].'</div> 
						';
					break;
					case "text":
						$out .= '
						<div class="control-group">  
							<label class="control-label" for="'.$value['id'].'">'.$value['title'].'</label>  
							<div class="controls">  
							  <input type="text"  class="'.$value['class'].'"  name="'.$value['name'].'" id="'.$value['id'].'" placeholder="'.$value['placeholder'].'" value="'.esc_html( stripslashes( $config[$value['name']] ) ).'">  
							  <p class="help-block">'.$value['sub_text'].'</p>  
							</div>  
						  </div> 
						';
					break;
					case "select":
						$out .= '
						<div class="control-group">  
							<label class="control-label" for="'.$value['id'].'">'.$value['title'].'</label>  
							<div class="controls">  
							  <select  style="'.$value['style'].'" class="'.$value['class'].'" name="'.$value['name'].'" id="'.$value['id'].'">' ; 
							  foreach( $value['value'] as $k => $v ){
								  $out .= '<option value="'.$k.'" '.( $config[$value['name']]  == $k ? ' selected ' : ' ' ).' >'.$v.'</option> ';
							  }
						$out .= '		
							  </select>  
							  <p class="help-block">'.$value['sub_text'].'</p> 
							</div>  
						  </div>  
						';
					break;
					case "checkbox":
						$out .= '
						<div class="control-group">  
							<label class="control-label" for="'.$value['id'].'">'.$value['title'].'</label>  
							<div class="controls">  
							  <label class="checkbox">  
								<input  class="'.$value['class'].'" type="checkbox" name="'.$value['name'].'" id="'.$value['id'].'" value="on" '.( $config[$value['name']] == 'on' ? ' checked ' : '' ).' > &nbsp; 
								'.$value['text'].'  
								<p class="help-block">'.$value['sub_text'].'</p> 
							  </label>  
							</div>  
						  </div>  
						';
					break;
					case "radio":
						$out .= '
						<div class="control-group">  
							<label class="control-label" for="'.$value['id'].'">'.$value['title'].'</label>  
							<div class="controls">';
								foreach( $value['value'] as $k => $v ){
									$out .= '
									<label class="radio">  
										<input  class="'.$value['class'].'" type="radio" name="'.$value['name'].'" id="'.$value['id'].'" value="'.$k.'" '.( $config[$value['name']] == $k ? ' checked ' : '' ).' >&nbsp;  
										'.$v.'  
										<p class="help-block">'.$value['sub_text'].'</p> 
									  </label> ';
								}
							$out .= '
							   
							</div>  
						  </div>  
						';
					break;
					case "textarea":
						$out .= '
						<div class="control-group">  
							<label class="control-label" for="'.$value['id'].'">'.$value['title'].'</label>  
							<div class="controls">  
							  <textarea style="'.$value['style'].'" class="'.$value['class'].'" name="'.$value['name'].'" id="'.$value['id'].'" rows="'.$value['rows'].'">'.esc_html( stripslashes( $config[$value['name']] ) ).'</textarea>  
							  <p class="help-block">'.$value['sub_text'].'</p> 
							</div>  
						  </div> 
						';
					break;
					case "multiselect":
						$out .= '
						<div class="control-group">  
							<label class="control-label" for="'.$value['id'].'">'.$value['title'].'</label>  
							<div class="controls">  
							  <select  multiple="multiple" style="'.$value['style'].'" class="'.$value['class'].'" name="'.$value['name'].'[]" id="'.$value['id'].'">' ; 
							  foreach( $value['value'] as $k => $v ){
								  $out .= '<option value="'.$k.'" '.( @in_array( $k, $config[$value['name']] )   ? ' selected ' : ' ' ).' >'.$v.'</option> ';
							  }
						$out .= '		
							  </select>  
							  <p class="help-block">'.$value['sub_text'].'</p> 
							</div>  
						  </div>  
						';
					break;
				}
			}
		}
			echo $out;
			?>

				
				  <div class="form-actions">  
					<button type="submit" class="btn btn-primary">Save Settings</button>  
				  </div>  
				</fieldset>  

		</form>

		</div>
		<?php
	}
}	
}	
	
 
 
$config_big = 
array(

	array(
		'type' => 'submenu',
		'parent_slug' => 'edit.php?post_type=taro_card',
		'page_title' => __('Taro Settings', $locale_taro),
		'menu_title' => __('Taro Settings', $locale_taro),
		'capability' => 'edit_published_posts',
		'menu_slug' => 'wrp_config',

		'parameters' => array(
			array(
				'name' => 'background_image',
				'type' => 'text',
				'title' => __('Background Image', $locale_taro ),
				'placeholder' => '',
				'sub_text' => '',
				'style' => ''
			),
			array(
				'name' => 'card_shirt',
				'type' => 'text',
				'title' => __('Card Shirt', $locale_taro ),
				'placeholder' => '',
				'sub_text' => '',
				'style' => ''
			),
			array(
				'name' => 'allow_rotation',
				'type' => 'checkbox',
				'title' => __('Allow Rotation', $locale_taro ),
				'placeholder' => '',
				'sub_text' => '',
				'style' => ''
			),
			array(
				'name' => 'button_text',
				'type' => 'text',
				'title' => __('Button Text', $locale_taro ),
				'placeholder' => '',
				'sub_text' => '',
				'style' => ''
			),
		)
	)
); 
new vooSettingsClass( $config_big, $this->locale ); 
?>