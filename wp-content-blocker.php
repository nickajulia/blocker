<?php
/*
Plugin Name: wp-content-blocker
Plugin URI: http://mindheros.com
Description: Get subscribers without gating your content - Mindheros.com
Version: 1.1
Author: Nick Julia
Author URI: http://mindheros.com
Stable tag: 1.12
*/

//error_reporting(E_ALL);
//ini_set('display_errors', 'On');


if( !class_Exists('vooMainStart') ){
	class vooMainStart{
		var $locale;
		function __construct( $locale, $includes, $path ){
			$this->locale = $locale;
			
			foreach( $includes as $single_path ){
				include( $path.$single_path );				
			}
			
		}
	}
}




new vooMainStart('wcb', array(
	'modules/scripts.php',
	'modules/cpt.php',
	'modules/meta_box.php'
), dirname(__FILE__).'/' );

 

#include('modules/functions.php');
include('modules/shortcodes.php');
#include('modules/settings.php');
#include('modules/meta_box.php');
#include('modules/widgets.php');
include('modules/hooks.php');
#include('modules/cpt.php');
#include('modules/scripts.php');
#include('modules/ajax.php');
 
?>