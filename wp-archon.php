<?php

/**
 * Plugin Name: WP-Heschl
 * Plugin URI: https://rayremnant.xyz
 * Description: Organizing and viewing data for affiliate marketing
 * Version: 1.0
 * Author: RayRemnant
 * Author URI: https://rayremnant.xyz
 * Text Domain: wp-heschl
* Domain Path: /languages
 **/

define('PLUGIN_DIR', plugin_dir_path( __FILE__ ));
define('PLUGIN_URL', plugin_dir_url( __FILE__ ));
define('UPLOAD_DIR', wp_upload_dir()); //use the ['url'] element

//include 'new-functions.php';

//include 'wp-blocks/block-product.php';
//include 'wp-blocks/block-pc-build.php';
//include 'wp-blocks/block-mini-box.php';
//include 'wp-blocks/block-graph-grid.php';
include 'wp-blocks/archon-product.php';
include 'wp-blocks/archon-notes.php';
include 'omni-atlas.php';

/* function wp_archon_load_textdomain() {
  load_plugin_textdomain( 'wp-archon', false, basename( PLUGIN_DIR ) . '/languages' ); 
}

add_action( 'plugins_loaded', 'wp_archon_load_textdomain' ); */