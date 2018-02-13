<?php
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
/*
Plugin Name: Post Cost Calculator
Author: Parker Westfall
Version: 1.0
*/

//enqueue script and styles
function enqueue_post_calc_scripts($hook) {
  if (is_admin()) {
    wp_register_script( 'post-calc', plugin_dir_url(__FILE__) . '/assets/post-calc.js');
    wp_enqueue_script('post-calc');
  }
  if ( 'toplevel_page_post-cost-calculator' != $hook ) {
    return;
  }

  wp_enqueue_script( 'jquery-ui-datepicker', array( 'jquery' ) );
  //wp_register_script( 'post-calc', plugin_dir_url(__FILE__) . '/assets/post-calc.js');
  wp_register_style('jquery-ui', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
  wp_enqueue_style( 'jquery-ui' );
  wp_register_style('post-calc-styles', plugin_dir_url(__FILE__) . '/assets/post-calc-styles.css');
  wp_enqueue_style( 'post-calc-styles' );
  //wp_enqueue_script('post-calc');
}

add_action('admin_enqueue_scripts', 'enqueue_post_calc_scripts');

require_once( plugin_dir_path( __FILE__ ) . '/includes/calc_admin_page.php');
require_once( plugin_dir_path( __FILE__ ) . '/includes/post-cost-calc-class.php');
?>
