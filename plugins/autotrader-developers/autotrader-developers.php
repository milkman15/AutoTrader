<?php
/**
 * AutoTrader Developers
 *
 *
 * @since             1.0.0
 *
 * @wordpress-plugin
 * Plugin Name:       AutoTrader Developers
 * Plugin URI:        https://autotrader.ca
 * Description:       AutoTrader Exercise
 * Version:           1.0.0
 * Author:            Brant Barton
 * Author URI:        https://brantmagic.com
 * License:           GPL-3.0
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       autotrade-developers
 * Domain Path:       /languages
 * Network:              true
 */

function run_better_search_replace()
{

    define( 'ATD_FILE', __FILE__ );
    define( 'ATD_PATH', plugin_dir_path( ATD_FILE ) );
    define( 'ATD_URL', plugin_dir_url( ATD_FILE ) );
    define( 'ATD_VERSION', '1.0.0' );

    require ATD_PATH . 'lib/admin/class-atd-admin.php';
    // $plugin = new Better_Search_Replace();
    // $plugin->run();

}

add_action( 'after_setup_theme', 'run_better_search_replace' );
