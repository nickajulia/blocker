<?php  
add_shortcode( 'locker', 'wcb_locker' );
function wcb_locker( $atts, $content = null ){
	
	$id = $atts['id'];
	
	$out .= '
	<style>
	.locker_container{
		overflow: hidden;
		height: 350px;
		position: relative;
		border: 1px solid #ccc;
		padding: 10px;
		border-radius: 5px;
	}
	.locker_container .content_overlap{
		position:absolute;
		lefT:0px;
		right:0px;
		bottom:0px;
		top:0px;
		z-index:100000;
		padding:20px;
		background: rgba(255,255,255, 0.95);
	}


	.locker_container .inner_locker{
		display1:none;
	}
	.locker_container .close_block{
		position:absolute;
		bottom:0px;
		lefT:0px;
		right:0px;
		text-align:center;
		padding:15px;
	}
	.locker_container .close_block .out_text{
		cursor:pointer;
		font-weight:bold;
	}
	</style>
	<div class="locker_container">	
		<div class="inner_locker">
		'.$content.'
		</div>	
		<div class="content_overlap">
			'.get_post( $id )->post_content.'
			'.( get_post_meta( $id, 'hide_close', true ) != 'on' ? '<div class="close_block"><span class="out_text">'.get_post_meta( $id, 'np_tnx_code', true ).'</span></div>' : '' ).'
		</div>
	</div>
	';
	//var_dump( $content );
	return $out;	
}

?>