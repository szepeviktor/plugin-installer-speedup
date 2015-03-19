<?php
/*
Plugin Name: Plugin Installer Speedup
Version: 0.2
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
        add_action( 'admin_menu', array( $this, 'download_menu' ) );
        add_action( 'admin_bar_menu', array( $this, 'new_content' ), 71 );

        add_action( 'admin_enqueue_scripts', array( $this, 'install_script_styles' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'upload_script_styles' ) );
        add_action( 'update-custom_' . 'download-plugin', array( $this, 'download_plugin' ) );
    }


    /**
     * Install plugin from a URL.
     *
     * A mix of install-plugin and upload-plugin actions from wp-admin/update.php:93.
     */
    public function download_plugin() {

        if ( ! current_user_can( 'upload_plugins' ) ) {
            wp_die( __( 'You do not have sufficient permissions to install plugins on this site.' ) );
        }

        check_admin_referer('plugin-download');

        require_once( ABSPATH . 'wp-admin/admin-header.php' );

        $download_url = esc_url_raw( $_REQUEST['pluginurl'] );

        // Remove "-master" from GitHub URL-s
        if ( false !== strstr( $download_url, '//github.com/' ) ) {
            add_filter( 'upgrader_source_selection', array( $this, 'remove_github_master' ), 9, 3 );
        }

        $type  = 'web';
        $title = sprintf( __( 'Installing Plugin from URL: %s' ), esc_html( $download_url ) );
        $url   = 'update.php?action=install-plugin';
        $nonce = 'plugin-download';

        $upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce' ) ) );
        $upgrader->install( $download_url );

        include( ABSPATH . 'wp-admin/admin-footer.php' );
    }


    public function upload_script_styles( $hook ) {

        if (  $this->admin_menu !== $hook ) {
            return;
        }

        $style = '.plugins_page_plugin-download .download-plugin-form { background: none repeat scroll 0 0 #fafafa;
            border: 1px solid #e5e5e5; margin: 30px auto; max-width: 380px; padding: 30px;}';

        wp_add_inline_style( 'wp-admin', $style );
    }

    public function install_script_styles( $hook ) {

        if ( 'plugin-install.php' !== $hook ) {
            return;
        }

        $style = '.plugin-install-php #search-submit { clip:auto; height:28px;
            position:static; vertical-align:baseline; width:auto; }';

        wp_add_inline_style( 'wp-admin', $style );

        // no inline scripting in WP
        wp_enqueue_script(
            'plugin-installer-speedup',
            plugin_dir_url( __FILE__ ) . 'js/installer.js',
            array( 'jquery' ),
            '1.0',
            true
        );
    }

    public function upload_fav_menu() {

        global $submenu;

        if ( ! is_multisite() ) {
            // hack into the Settings menu
            $submenu['plugins.php'][9] = array( _x( 'Favorites', 'Plugin Installer' ),
                'install_plugins', 'plugin-install.php?tab=favorites' );
            $submenu['plugins.php'][11] = array( __( 'Upload Plugin' ),
                'install_plugins', 'plugin-install.php?tab=upload' );
            ksort( $submenu['plugins.php'] );
        }
    }

    public function download_form() {

?>
<div class="wrap">
<h2>Upload Plugin from URL
<?php

    $href = self_admin_url( 'plugin-install.php' );
    $text = _x( 'Browse', 'plugins' );
    echo ' <a href="' . $href . '" class="upload add-new-h2">' . $text . '</a>';

?>
</h2>
<div class="download-plugin upload-plugin">
    <p class="install-help"><?php _e( 'If you have a plugin in a .zip hosted somewhere, you may install it by entering the URL here.' ); ?></p>
    <form method="post" class="download-plugin-form" action="<?php echo self_admin_url( 'update.php?action=download-plugin' ); ?>">
        <?php wp_nonce_field( 'plugin-download'); ?>
        <label class="screen-reader-text" for="pluginurl"><?php _e('Plugin URL'); ?></label>
        <input type="url" id="pluginurl" name="pluginurl" autofocus required />
        <?php submit_button( __( 'Install Now' ), 'button', 'install-download-plugin-submit', false ); ?>
    </form>
</div>
</div>
<?php //"

    }

    public function download_menu() {

        $this->admin_menu = add_plugins_page( __( 'Add Plugins' ), 'Upload from URL', 'install_plugins', 'plugin-download', array( $this, 'download_form' ) );
    }

    public function new_content() {

        global $wp_admin_bar;

        $wp_admin_bar->add_menu( array(
            'parent'    => 'new-content',
            'id'        => 'plugin-install',
            'title'     => __('Plugin'),
            'href'      => self_admin_url( 'plugin-install.php' )
        ) );

    }

    public function remove_github_master( $source, $remote_source, $that ) {

        global $wp_filesystem;

        $gh = '-master';

        $new_source = $this->remove_trailing_part( $source, $gh );

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
            // try with a slash
            if ( substr( $haystack, -$length - 1 ) === $needle . '/' ) {
                return substr( $haystack, 0, -$length - 1 ) . '/';
            }
            return  $haystack;
        }

        return substr( $haystack, 0, -$length );
    }

}

new O1_Plugin_Speedups();
