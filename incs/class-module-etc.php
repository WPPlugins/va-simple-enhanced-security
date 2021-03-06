<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * VA Simple Enhanced Security module - etc.
 *
 * @package WordPress
 * @subpackage VA Simple Enhanced Security
 * @author KUCKLU <kuck1u@visualive.jp>
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPLv2.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-2.0.txt
 */
class VASES_MODUL_ETC {

    /**
     * Holds the singleton instance of this class
     */
    static $instance = false;

    /**
     * Singleton
     * @static
     */
    public static function init() {
        if ( ! self::$instance ) {
            self::$instance = new VASES_MODUL_ETC;
        }

        return self::$instance;
    }

    /**
     * Plugin settings
     * @return array
     */
    function __construct() {
        $settings                      = VASES_MODUL_SETTINGS_API::init();
        $flug_remove_body_class        = $settings->args['remove_body_class'];
        $flug_xmlrpc_login_disallow    = $settings->args['xmlrpc_login_disallow'];
        $flug_xmlrpc_pingback_disallow = $settings->args['xmlrpc_pingback_disallow'];
        $flug_file_edit_disallow       = $settings->args['file_edit_disallow'];
        $flug_unfiltered_html_disallow = $settings->args['unfiltered_html_disallow'];
        $flug_auto_update_plugin       = $settings->args['auto_update_plugin'];
        $flug_auto_update_theme        = $settings->args['auto_update_theme'];

        /**
         * Delete the information of author from body class.
         * @param  array $classes Array of the css class
         * @return array          Array of the new css class
         */
        if ( $flug_remove_body_class == true ) {
            add_filter( 'body_class', array( &$this, 'remove_body_class' ) );
        }
        /**
         * Disallow xmlrpc login.
         */
        if ( $flug_xmlrpc_login_disallow == true ) {
            add_action( 'xmlrpc_enabled', '__return_false' );
        }
        /**
         * Disallow xmlrpc pingback.
         * @param  array $methods An array of XML-RPC methods.
         * @return array          An array of XML-RPC methods.
         */
        if ( $flug_xmlrpc_pingback_disallow == true ) {
            add_filter( 'xmlrpc_methods', array( &$this, 'xmlrpc_methods' ) );
        }
        /**
         * Disallow the file editors.
         */
        add_action( 'admin_init', function () use ( $flug_file_edit_disallow ) {
            global $wp_roles;
        } );
        /**
         * Disallow unfiltered_html for all users, even admins and super admins.
         */
        if ( $flug_unfiltered_html_disallow == true && ! defined( 'DISALLOW_UNFILTERED_HTML' ) ) {
            define( 'DISALLOW_UNFILTERED_HTML', true );
        }
        /**
         * Automatically update the plugin.
         */
        if ( $flug_auto_update_plugin == true ) {
            add_filter( 'auto_update_plugin', '__return_true', 99999 );
        }
        /**
         * Automatically update the theme.
         */
        if ( $flug_auto_update_theme == true ) {
            add_filter( 'auto_update_theme',  '__return_true', 99999 );
        }
    }
    /**
     * Remove body class
     * @param  array $classes Array of the css class
     * @return array          Array of the new css class
     */
    public static function remove_body_class( $classes ) {
        $subject     = $classes;
        $pattern     = array( '/\A(author\-[\w+\-]*)\z/i' );
        $replacement = array( '' );
        $classes     = preg_replace( $pattern, $replacement, $subject );

        return array_values( array_filter( $classes ) );
    }

    /**
     * Disallow xmlrpc pingback.
     * @param  array $methods An array of XML-RPC methods.
     * @return array          An array of XML-RPC methods.
     */
    public static function xmlrpc_methods( $methods ) {
        unset( $methods['pingback.ping'] );
        unset( $methods['pingback.extensions.getPingbacks'] );
        return $methods;
    }
}
