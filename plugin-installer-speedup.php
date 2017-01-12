<?php
/*
Plugin Name: Plugin Installer Speedup
Version: 0.2.2
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

class O1_Plugin_Speedups {

    private $admin_menu;

    public function __construct() {

        add_filter( 'install_plugins_table_api_args_featured', '__return_false' );
        add_action( 'admin_init', array( $this, 'upload_fav_menu' ) );
        add_action( 'admin_bar_menu', array( $this, 'new_content' ), 71 );

        add_action( 'admin_enqueue_scripts', array( $this, 'install_script_styles' ) );
        add_action( 'load-update.php', array( $this, 'hook_modify_source' ) );
    }

    public function install_script_styles( $hook ) {

        if ( 'plugin-install.php' !== $hook ) {
            return;
        }

        $style = '.plugin-install-php #search-submit { display: inline-block;
            clip:auto; height:28px; position:static; vertical-align:baseline;
            width:auto; }';

        wp_add_inline_style( 'wp-admin', $style );

        wp_enqueue_script(
            'plugin-installer-speedup',
            plugin_dir_url( __FILE__ ) . 'js/installer.js',
            array( 'jquery' ),
            '1.0.1',
            true
        );
    }

    public function upload_fav_menu() {

        global $submenu;

        if ( ! is_multisite() ) {
            // Hack into the Settings menu
            $submenu['plugins.php'][9] = array(
                _x( 'Favorites', 'Plugin Installer' ),
                'install_plugins',
                'plugin-install.php?tab=favorites',
            );
            $submenu['plugins.php'][11] = array(
                __( 'Upload Plugin' ),
                'install_plugins',
                'plugin-install.php?tab=upload',
            );
            ksort( $submenu['plugins.php'] );
        }
    }

    public function new_content() {

        global $wp_admin_bar;

        $wp_admin_bar->add_menu( array(
            'parent'    => 'new-content',
            'id'        => 'plugin-install',
            'title'     => __( 'Plugin' ),
            'href'      => self_admin_url( 'plugin-install.php' ),
        ) );

    }

    public function hook_modify_source() {

        if ( 'upload-plugin' !== $_GET['action'] ) {
            return;
        }

        // Remove "-master" from GitHub URL-s
        add_filter( 'upgrader_source_selection', array( $this, 'remove_github_master' ), 9, 3 );
    }

    public function remove_github_master( $source, $remote_source, $that ) {

        global $wp_filesystem;

        $ghm = '-master';

        $new_source = $this->remove_trailing_part( $source, $ghm );

        if ( $wp_filesystem->move( $source, $new_source ) ) {
            return $new_source;
        }

        return $source;
    }

    private function remove_trailing_part( $haystack, $needle ) {

        $length = strlen( $needle );
        if ( 0 === $length ) {
            return  $haystack;
        }

        if ( substr( $haystack, -$length ) !== $needle ) {
            // Try with a slash too
            if ( substr( $haystack, -$length - 1 ) === $needle . '/' ) {
                return substr( $haystack, 0, -$length - 1 ) . '/';
            }
            return  $haystack;
        }

        return substr( $haystack, 0, -$length );
    }

}

new O1_Plugin_Speedups();
