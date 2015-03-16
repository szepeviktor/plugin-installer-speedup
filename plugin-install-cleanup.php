<?php
/*
Plugin Name: Plugin Installer Cleanup
Version: 0.1
Description: Speedups: dont't load featured plugins, make Search Plugins button visible, skip plugin install confirmation.
Plugin URI: https://wordpress.org/plugins/plugin-install-cleanup/
Author: Viktor Szépe
Author URI: http://www.online1.hu/webdesign/
License: GNU General Public License (GPL) version 2
GitHub Plugin URI: https://github.com/szepeviktor/plugin-install-cleanup
*/

add_filter( 'install_plugins_table_api_args_featured', 'o1_disable_featured_plugins_tab' );
add_action( 'admin_enqueue_scripts', 'o1_plugin_cleanup_script_styles' );

function o1_disable_featured_plugins_tab( $args ) {

    return false;
}

function o1_plugin_cleanup_script_styles( $hook ) {

    if ( 'plugin-install.php' !== $hook ) {
        return;
    }

    $style = '.plugin-install-php #search-submit { clip:auto; height:28px; position:static; vertical-align:baseline; width:auto; }';
    wp_add_inline_style( 'wp-admin', $style );

    wp_enqueue_script(
        'plugin-install-cleanup',
        plugin_dir_url( __FILE__ ) . 'js/plugin-install-cleanup.js',
        array( 'jquery' ),
        '1.0',
        true
    );
}
