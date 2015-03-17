<?php
/*
Plugin Name: Plugin Installer Speedup
Version: 0.1
Description: Speedups: dont't load featured plugins, make Search Plugins button visible, skip plugin install confirmation.
Plugin URI: https://wordpress.org/plugins/plugin-installer-speedup/
Author: Viktor Szépe
Author URI: http://www.online1.hu/webdesign/
License: GNU General Public License (GPL) version 2
GitHub Plugin URI: https://github.com/szepeviktor/plugin-installer-speedup
*/

if ( ! function_exists( 'add_filter' ) ) {
    ob_get_level() && ob_end_clean();
    header( 'Status: 403 Forbidden' );
    header( 'HTTP/1.1 403 Forbidden' );
    exit();
}

add_filter( 'install_plugins_table_api_args_featured', 'o1_disable_featured_plugins_tab' );
add_action( 'admin_enqueue_scripts', 'o1_plugin_speedup_script_styles' );
add_action( 'admin_init', 'o1_plugin_speedup_upload_menu' );

function o1_disable_featured_plugins_tab( $args ) {

    return false;
}

function o1_plugin_speedup_script_styles( $hook ) {

    if ( 'plugin-install.php' !== $hook ) {
        return;
    }

    $style = '.plugin-install-php #search-submit { clip:auto; height:28px;
        position:static; vertical-align:baseline; width:auto; }';
    wp_add_inline_style( 'wp-admin', $style );

    // no inline scripting in WP
    wp_enqueue_script(
        'plugin-installer-speedup',
        plugin_dir_url( __FILE__ ) . 'js/plugin-installer-speedup.js',
        array( 'jquery' ),
        '1.0',
        true
    );
}

function o1_plugin_speedup_upload_menu() {

    global $submenu;

    if ( ! is_multisite() ) {
        // hack into the Settings menu
        $submenu['plugins.php'][11] = array( __( 'Upload Plugin' ),
            'install_plugins', 'plugin-install.php?tab=upload' );
        $submenu['plugins.php'][12] = array( _x( 'Favorites', 'Plugin Installer' ),
            'install_plugins', 'plugin-install.php?tab=favorites' );
    }
}
