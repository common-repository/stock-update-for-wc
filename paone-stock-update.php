<?php
/**
 * @package paone-stock-update
 */
/*
Plugin Name: Stock Update for WC
Plugin URI: https://endroit.in/
Description: Bulk update inventory/stock values on you woocommerce store.
Version: 1.0.0
Author: Pawan Priyadarshi
Author URI: https://endroit.in/pawan-priyadarshi
License: GPLv2 or later
Text Domain: PAONE_SU-stock-update
*/

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright 2019-2020 Pawan Priyadarshi.
*/
if (!defined('ABSPATH')) {
    exit;
}
define('PAONE_SU_WP_PATH', plugin_dir_path(__FILE__));
define('PAONE_SU_WP_URL', plugins_url('', __FILE__));
define('PAONE_SU_LANG', 'PAONE_SU_');
/*
 * Registration Hooks
 */
register_activation_hook(__FILE__, 'PAONE_SU_install');
register_deactivation_hook(__FILE__, 'PAONE_SU_deactivate');
register_uninstall_hook(__FILE__, 'PAONE_SU_uninstall');
/*
 * Add Admin menu
 */
add_action('admin_menu', 'PAONE_SU_menu');
function PAONE_SU_menu()
{
    add_menu_page('Product Stock Update for WC', 'Stock Update', 'manage_options', 'wc-product-stock-update', 'PAONE_SU_init', plugin_dir_url(__FILE__) . 'images/icon.png');
}

function PAONE_SU_init()
{
    if (is_admin()) {
        require_once(PAONE_SU_WP_PATH . 'admin/class.paone_su_admin_model.php');
        require_once(PAONE_SU_WP_PATH . 'admin/class.paone_su_admin.php');
        new PaoneSuAdmin();
    }
}

/*
 * Function to run insall scripts
 */
function PAONE_SU_install()
{

}

/*
 * Function to run scripts on plugin deactivation
 */
function PAONE_SU_deactivate()
{

}

/*
 * Function to run scripts on plugin uninstallation
 */
function PAONE_SU_uninstall()
{

}

//if (!is_admin()) {
//    require_once(PAONE_SU_WP_PATH . 'frontend/class.PAONE_SU-frontend.php');
//    require_once(PAONE_SU_WP_PATH . 'cart/class.cart.php');
//    new PAONE_SU_Frontend();
//}
//setting link
function PAONE_SU_settings_link( $links ) {
    $settings_link = '<a href="admin.php?page=PAONE_SU>' . __( 'Settings' ) . '</a>';


    array_push( $links, $settings_link );
    return $links;
}
$plugin = plugin_basename( __FILE__ );
add_filter( "plugin_action_links_$plugin", 'PAONE_SU_settings_link' );