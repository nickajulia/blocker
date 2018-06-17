<?php 
if( !class_exists( 'vooMetaBox' ) ){
	class vooMetaBox{
		
		private $metabox_parameters = null;
		private $fields_parameters = null;
		private $data_html = null;
		
		function __construct( $metabox_parameters , $fields_parameters){
			$this->metabox_parameters = $metabox_parameters;
			$this->fields_parameters = $fields_parameters;
 
			add_action( 'add_meta_boxes', array( $this, 'add_custom_box' ) );
			add_action( 'save_post', array( $this, 'save_postdata' ) );
		}
		
		function add_custom_box(){
			add_meta_box( 
				'custom_meta_editor_'.rand( 100, 999 ),
				$this->metabox_parameters['title'],
				array( $this, 'custom_meta_editor' ),
				$this->metabox_parameters['post_type'] , 
				$this->metabox_parameters['position'], 
				$this->metabox_parameters['place']
			);
		}
		function custom_meta_editor(){
			global $post;
			
			$out = '

			<div class="tw-bs">
				<div class="form-horizontal ">';
			
			foreach( $this->fields_parameters as $single_field){
			 
				switch( $single_field['type'] ){
					
					case "shortcode":
					$out .= '<div class="control-group">  
						<label class="control-label" for="input01">'.$single_field['title'].'</label>  
						<div class="controls">  
						  <input type="text" class="input-xlarge" name="'.$single_field['name'].'" id="'.$single_field['name'].'" 
						  value="['.$single_field['name'].' id=\''.$post->ID.'\'][/'.$single_field['name'].']"
						  
						  >  
						</div>  
					  </div> ';	
					break;
					
					
					case "textarea":
					$out .= '<div class="control-group">  
						<label class="control-label" for="input01">'.$single_field['title'].'</label>  
						<div class="controls">  
						  <textarea type="text" class="input-xlarge" style="'.$single_field['style'].'" name="'.$single_field['name'].'" id="'.$single_field['name'].'" >'.htmlentities( get_post_meta( $post->ID, $single_field['name'], true ) ).'</textarea>  
						</div>  
					  </div> ';	
					break;
					case "text":
					$out .= '<div class="control-group">  
						<label class="control-label" for="input01">'.$single_field['title'].'</label>  
						<div class="controls">  
						  <input type="text" class="input-xlarge" name="'.$single_field['name'].'" id="'.$single_field['name'].'" value="'.get_post_meta( $post->ID, $single_field['name'], true ).'">  
						</div>  
					  </div> ';	
					break;
					case "checkbox":
					$out .= '<div class="control-group">  
						<label class="control-label" for="input01">'.$single_field['title'].'</label>  
						<div class="controls">  
						  <input type="checkbox"   name="'.$single_field['name'].'" id="'.$single_field['name'].'" value="on" '.( get_post_meta( $post->ID, $single_field['name'], true ) == 'on' ? ' checked ' : '' ).' >  
						</div>  
					  </div> ';	
					break;
					case "select":
					$out .= '<div class="control-group">  
						<label class="control-label" for="input01">'.$single_field['title'].'</label>  
						<div class="controls">';

							$out .= '<select name="'.$single_field['name'].'">';
							foreach( $single_field['value'] as $key => $value ){
								$out .= '<option '.( get_post_meta( $post->ID, $single_field['name'], true ) == $key ? ' selected ' : '' ).' value="'.$key.'">'.$value;
							}
							$out .= '</select>';
						 
					$out .= '
						</div>  
					  </div> ';	
					break;
				}
			}		
			
					
					
			$out .= '
					</div>	
				</div>
				';	
			$this->data_html = $out;
			 
			$this->echo_data();
		}
		
		function echo_data(){
			echo $this->data_html;
		}
		
		function save_postdata( $post_id ) {
			global $current_user; 
			 if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
				  return;

			  if ( 'page' == $_POST['post_type'] ) 
			  {
				if ( !current_user_can( 'edit_page', $post_id ) )
					return;
			  }
			  else
			  {
				if ( !current_user_can( 'edit_post', $post_id ) )
					return;
			  }
			  /// User editotions

				if( get_post_type($post_id) == $this->metabox_parameters['post_type'] ){
					foreach( $this->fields_parameters as $single_parameter ){
						update_post_meta( $post_id, $single_parameter['name'], $_POST[$single_parameter['name']] );
					}
					
				}
				
			}
	}
}
$meta_box = array(
	'title' => 'Hide "Close" button',
	'post_type' => 'lockers',
	'position' => 'side',
	'place' => 'high'
);
$fields_parameters = array(
	array(
		'type' => 'checkbox',
		'title' => 'Show Close button',
		'name' => 'hide_close'
	),
	array(
		'type' => 'shortcode',
		'title' => 'Shortcode',
		'name' => 'locker'
	),
 
);		
new vooMetaBox( $meta_box, $fields_parameters); 

 
$meta_box = array(
	'title' => 'No Thanks code',
	'post_type' => 'lockers',
	'position' => 'advanced',
	'place' => 'high'
);
$fields_parameters = array(
	array(
		'type' => 'textarea',
		'title' => 'Code',
		'name' => 'np_tnx_code',
		'style' => 'height:300px; width:100%;'
	),
 
 
);		
new vooMetaBox( $meta_box, $fields_parameters); 
?>