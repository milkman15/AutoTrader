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

// admin actions
add_action( 'admin_menu', 'at_options_page' );

function at_options_page()
{
    add_options_page(
        'AutoTrader Developers',
        'AutoTrader Developers',
        'administrator',
        'autotrader_developers',
        // 'at_setup_options'
    );
}
// function at_setup_options()
// {
//     echo 'hello world';
// }

// Add field:
add_action( 'add_meta_boxes', 'add_developers_meta' );

function add_developers_meta()
{
    add_meta_box(
        'at_developers',
        'Developers',
        'at_developers_callback',
        'post',
        'side'
    );
}

function at_developers_callback( $post )
{
    wp_nonce_field( __FILE__, '_at_nonce' );
    ?>

    <?php

    $authors       = get_users( ['role' => 'author'] );
    $authors_meta  = get_post_meta( $post->ID, '_at_developers', true );
    $saved_authors = is_array( $authors_meta ) ? $authors_meta : [];

    if ( $authors ) {
        foreach ( $authors as $author ) { ?>
            <label for="at_developers_<?php echo $author->ID; ?>">
                <input type="checkbox" name="at_developers[]" id="at_developers_<?php echo $author->ID; ?>" value="<?php echo $author->ID; ?>" <?php echo in_array( $author->ID, $saved_authors ) ? 'checked="checked"' : ''; ?>>
                <?php echo $author->display_name; ?>
            </label>
            <br>
        <?php }
    }
}

function at_save_developer_meta( $post_id )
{
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    if (  ( isset( $_POST['_at_nonce'] ) ) && ( !wp_verify_nonce( $_POST['_at_nonce'], __FILE__ ) ) ) {
        return;
    }

    if (  ( isset( $_POST['post_type'] ) ) && ( 'post' == $_POST['post_type'] ) ) {
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    if ( !empty( $_POST['at_developers'] ) ) {
        update_post_meta( $post_id, '_at_developers', $_POST['at_developers'] );
    } else {
        delete_post_meta( $post_id, '_at_developers' );
    }
}

add_action( 'save_post', 'at_save_developer_meta' );

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