<?php
/**
 * Plugin Name: Softech Wp Clock
 * Plugin URI: https://wordpress.org/plugins/softech-wp-clock/
 * Description: Display a Time zone clock on your page/post set to your city's timezone. Choice of clocks, colors and sizes.
 * Version: 1.0.1
 * Author:      Softechure
 * Author URI:  https://softechure.com/
 * Text Domain: softech-wp-clock
 * Domain Path: /languages
 * License:     GPLv2
 
 Softech Wp Clock is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 2 of the License, or
 any later version.
 
 Softech Wp Clock is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License
 along with Softech Wp Clock. If not, see {License URI}.
*/


define( 'SOFTECH_WP_CLOCK_VERSION', '1.0.1' );
define( 'SOFTECH_WP_CLOCK_MINIMUM_WP_VERSION', '4.0' );
define( 'SOFTECH_WP_CLOCK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );


if ( ! version_compare( PHP_VERSION, '5.4', '>=' ) ) 
{
  add_action( 'admin_notices', 'softech_wp_clock_fail_php_version' );
} 
elseif ( ! version_compare( get_bloginfo( 'version' ), '4.0', '>=' ) ) 
{
    add_action( 'admin_notices', 'softech_wp_clock_fail_wp_version' );
} 
else 
{

function softech_wp_clock_textdomain() 
{
    // Set filter for plugin's languages directory
    $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

    // Load the translations
    load_plugin_textdomain( 'softech-wp-clock', false, $lang_dir );
}
add_action( 'init', 'softech_wp_clock_textdomain' );


require_once(dirname(__FILE__).'/list-wp-clock.php');
require_once(dirname(__FILE__).'/add-wp-clock.php');
require_once(dirname(__FILE__).'/add-shortcode.php');


/* ======= Register Plugin js. =========== */
function load_softech_wp_clock_js() 
{
    wp_register_script( 'softech-wp-clock', plugin_dir_url(__FILE__) . 'js/softech-wp-clock.js', '', '', true );
    wp_enqueue_script('softech-wp-clock');
    wp_enqueue_script('jquery');
}
add_action( 'admin_enqueue_scripts', 'load_softech_wp_clock_js' ); // It load the js file in admin area
add_action( 'wp_enqueue_scripts', 'load_softech_wp_clock_js' );


/* ======= Register Plugin style sheet. =========== */
function load_softech_wp_clock_style() 
{
    wp_register_style( 'softeh-wp-style', plugin_dir_url(__FILE__) . 'css/style.css', false, '1.0' );
    wp_enqueue_style('softeh-wp-style');
}
add_action( 'admin_enqueue_scripts', 'load_softech_wp_clock_style' );  // It load the style file in admin area
add_action( 'wp_enqueue_scripts', 'load_softech_wp_clock_style' );

/* ======= Register Plugin Google Fonts. =========== */
function softech_wp_clock_google_fonts() 
{
    $query_args = array(
        'family' => 'Share+Tech+Mono:400,700|Oswald:700'
    );
    wp_register_style( 'google_fonts', add_query_arg( $query_args, "https://fonts.googleapis.com/css?family=Share+Tech+Mono" ), array(), null );
}
add_action('wp_enqueue_scripts', 'softech_wp_clock_google_fonts');



/* ======= Register the Softech Wp Clock Menu =========== */

function softech_wp_clock_menu_register()
{
    global $softech_screen_hook;
    $softech_screen_hook = add_menu_page( __('Softech Wp Clock','softech-wp-clock'),       //Page Title
                  __('Softech Wp Clock','softech-wp-clock'),        //Menu Title
                  'manage_options', 
                  'softech-wp-all-zone-list',               //Menu Slug
                  'softech_wp_all_zone_list',           //Menu Page
                  'dashicons-clock', //Menu Icon
                  6
    );

    add_submenu_page( 'softech-wp-all-zone-list',            //Parent Menu Slug
                      __('Time Zones','softech-wp-clock'),                //Page Title
                      __('Time Zones','softech-wp-clock'),                //SubMenu Title
                      'manage_options', 
                      'softech-wp-all-zone-list',       //SubMenu Slug
                      'softech_wp_all_zone_list'   //SubMenu Page
    );

    add_submenu_page( 'softech-wp-all-zone-list',            //Parent Menu Slug
                      __('Add New','softech-wp-clock'),                //Page Title
                      __('Add New','softech-wp-clock'),              //SubMenu Title
                      'manage_options', 
                      'softech-wp-clock-new-zone',       //SubMenu Slug
                      'softech_wp_clock_form_page_handler'   //SubMenu Page
    );

    add_action("load-".$softech_screen_hook, "softech_wp_clock_screen_option_list");
}
add_action( 'admin_menu', 'softech_wp_clock_menu_register' );


/* ======= Create The DataBase On Plugin Activation=========== */

global $softech_clock_db_version;
$softech_clock_db_version = '1.0.1'; // version changed from 1.0 to 1.1

function softech_clock_install()
{
    global $wpdb;
    global $softech_clock_db_version;

    $table_name = $wpdb->prefix . 'softech_clock'; // do not forget about tables prefix
    $sql = "CREATE TABLE " . $table_name . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        time_zone varchar(1000) NOT NULL,
        shortcode varchar(100) NOT NULL,
        clock_css text NOT NULL,
        date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
    );";

    // we do not execute sql directly
    // we are calling dbDelta which cant migrate database
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);

    // save current database version for later use (on upgrade)
    add_option('softech_clock_db_version', $softech_clock_db_version);

    /**
     * [OPTIONAL] Example of updating to 1.1 version
     *
     * If you develop new version of plugin just increment $softech_clock_db_version variable and add following block of code
     * must be repeated for each new version
     * in version 1.1 we change email field
     * to contain 200 chars rather 100 in version 1.0
     * and again we are not executing sql
     * we are using dbDelta to migrate table changes
     */
    $installed_ver = get_option('softech_clock_db_version');
    if ($installed_ver != $softech_clock_db_version) 
    {
        $sql = "CREATE TABLE " . $table_name . " (
        id int(11) NOT NULL AUTO_INCREMENT,
        name varchar(100) NOT NULL,
        time_zone varchar(1000) NOT NULL,
        shortcode varchar(100) NOT NULL,
        clock_css text NOT NULL,
        date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY  (id)
        );";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // notice that we are updating option, rather than adding it
        update_option('softech_clock_db_version', $softech_clock_db_version);
    }

}
register_activation_hook(__FILE__, 'softech_clock_install');

/**
 * Trick to update plugin database, see docs
 */
function softech_clock_update_db_check()
{
    global $softech_clock_db_version;
    if (get_site_option('softech_clock_db_version') != $softech_clock_db_version) 
    {
        softech_clock_install();
    }
}
add_action('plugins_loaded', 'softech_clock_update_db_check');



register_deactivation_hook( __FILE__, 'softech_clock_remove_database' );
function softech_clock_remove_database() 
{
     global $wpdb;
     $table_name = $wpdb->prefix . 'softech_clock';
     $sql = "DROP TABLE IF EXISTS $table_name";
     $wpdb->query($sql);
     delete_option("my_plugin_db_version");
}
}

/**
 * Softech Wp Clock admin notice for minimum PHP version.
 * Warning when the site doesn't have the minimum required PHP version.
 */
function softech_wp_clock_fail_php_version() 
{
    /* translators: %s: PHP version */
    $message = sprintf( esc_html__( 'Softech Wp Clock requires PHP version %s+, plugin is currently NOT RUNNING.', 'softech-wp-clock' ), '5.4' );
    $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
    echo wp_kses_post( $html_message );
}

/**
 * Softech Wp Clock admin notice for minimum WordPress version.
 * Warning when the site doesn't have the minimum required WordPress version.
 */
function softech_wp_clock_fail_wp_version() 
{
    /* translators: %s: WordPress version */
    $message = sprintf( esc_html__( 'Softech Wp Clock requires WordPress version %s+. Because you are using an earlier version, the plugin is currently NOT RUNNING.', 'softech-wp-clock' ), '4.6' );
    $html_message = sprintf( '<div class="error">%s</div>', wpautop( $message ) );
    echo wp_kses_post( $html_message );
}
?>