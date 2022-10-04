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

    // require ATD_PATH . 'includes/admin/class-atd-admin.php';

}

add_action( 'after_setup_theme', 'run_better_search_replace' );

add_action( 'wp_enqueue_scripts', 'at_register_scripts' );

function at_register_scripts()
{
    wp_register_style( 'at-style-public', ATD_URL . '/includes/assets/public/css/style.css' );
    wp_register_style( 'at-slider-style-public', ATD_URL . '/includes/assets/public/css/slider.css' );
    wp_register_script( 'owl-scripts', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', ['jquery'], '2.3.4', true );
    wp_register_style( 'owl-styles', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css' );
    wp_register_style( 'owl-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css' );
    wp_register_script( 'custom-scripts', ATD_URL . '/includes/assets/public/js/custom.js', ['jquery', 'owl-scripts'], '1.0.0', true );
}

add_action( 'admin_enqueue_scripts', 'at_admin_scripts' );

function at_admin_scripts( $hook )
{
    // if ( 'at-gallery-options' === $hook ) {
    wp_enqueue_style( 'at-options', ATD_URL . '/includes/assets/admin/css/options.css' );
    wp_enqueue_style( 'dropzone', 'https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.0.1/min/dropzone.min.css' );
    wp_enqueue_script( 'jquery-ui-scripts', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js', ['jquery'], '1.13.2', true );
    wp_enqueue_style( 'jquery-ui-styles', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/jquery-ui.min.css' );
    wp_enqueue_style( 'jquery-ui-theme', 'https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.13.2/themes/base/theme.min.css' );
    wp_enqueue_script( 'at-options', ATD_URL . '/includes/assets/admin/js/options.js', ['jquery-ui-scripts'], '1.0.0', true );
    wp_enqueue_media();
    // }

}

function at_gallery_secttings_page()
{
    add_settings_section( "at-gallery-section", "AutoTrader Gallery", null, "at-gallery-files" );
    add_settings_field( "at-gallery-file", "Gallery", "at_gallery_display", "at-gallery-files", "at-gallery-section" );
    register_setting( "at-gallery-section", "at-gallery-file" );
}

function at_gallery_display()
{
    $images     = get_option( 'at-gallery-file' );
    $buttonText = 'Add more images';
    $inputValue = '';
    if ( !$images ) {
        $buttonText = 'Upload image(s)';
    } else {
        if ( count( $images ) > 0 ) {
            $inputValue = $images[0];
        }
    }
    ?>
        <a href="#" class="button at-upload">
            <?php echo $buttonText; //phpcs:ignore ?>
        </a>

        <input type="hidden" id="at-gallery-file" name="at-gallery-file[]" value="<?php echo esc_attr( $inputValue ); ?>">
<?php }

add_action( "admin_init", "at_gallery_secttings_page" );

function at_gallery_page()
{
    ?>
      <div class="wrap">
         <h1>AutoTrader Gallery Settings</h1>

         <form method="post" action="options.php">
            <?php

    settings_fields( "at-gallery-section" );
    do_settings_sections( "at-gallery-files" );
    ?>

            <div id="at-gallery-preview">
                <?php $images = get_option( 'at-gallery-file' );
    if ( $images && isset( $images ) ) {
        if ( count( $images ) > 0 ):
            $splitImages = explode( ',', $images[0] );
            foreach ( $splitImages as $image_id ):
                if ( $image = wp_get_attachment_image_url( $image_id, 'medium' ) ): ?>
				            <div class="at-gallery-image" data-attachId="<?php echo esc_attr( $image_id ); ?>">
				                <a href="#" class="at-remove">Remove image</a>
				                <img src="<?php echo esc_url( $image ); ?>" />
				            </div>
				            <?php endif; ?>
																																																						                <?php endforeach; ?>
            <?php endif; ?>
        <?php } ?>
            </div>

            <?php submit_button(); ?>
         </form>
        </div>
   <?php
}

function menu_item()
{
    add_submenu_page( "options-general.php", "AutoTrader Gallery", "AutoTrader Gallery", "manage_options", "at-gallery-options", "at_gallery_page" );
}

add_action( "admin_menu", "menu_item" );

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
            <label for="at_developers_<?php echo esc_attr( $author->ID ); ?>">
                <input type="checkbox" name="at_developers[]" id="at_developers_<?php echo esc_attr( $author->ID ); ?>" value="<?php echo esc_attr( $author->ID ); ?>" <?php echo in_array( $author->ID, $saved_authors ) ? 'checked="checked"' : ''; ?>>
                <?php echo esc_html( $author->display_name ); ?>
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

    if (  ( isset( $_POST['_at_nonce'] ) ) && ( !wp_verify_nonce( sanitize_key( $_POST['_at_nonce'] ), __FILE__ ) ) ) {
        return;
    }

    if (  ( isset( $_POST['post_type'] ) ) && ( 'post' == $_POST['post_type'] ) ) {
        if ( !current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    if ( !empty( sanitize_key( $_POST['at_developers'] ) ) ) {
        update_post_meta( $post_id, '_at_developers', $_POST['at_developers'] );
    } else {
        delete_post_meta( $post_id, '_at_developers' );
    }
}

add_action( 'wp_footer', 'at_generate_developers_feed' );

function at_generate_developers_feed()
{
    global $post;

    $authors_meta = get_post_meta( $post->ID, '_at_developers', true );

    if ( !empty( $authors_meta ) ) {
        echo '<div class="at-developers-feed">';
        $authors = get_users( ['role' => 'author', 'include' => $authors_meta] );
        foreach ( $authors as $author ) {
            echo sprintf( '<div class="author"><a href="%3$s"><img src="%2$s"><span class="at-author-name">%1$s</span></a></div>',
                esc_html( $author->display_name ),
                esc_url( get_avatar_url( $author->ID ) ),
                esc_url( get_author_posts_url( $author->ID ) )
            );
        }
        echo '</div>';
    }
}

add_action( 'save_post', 'at_save_developer_meta' );

add_shortcode( 'at_slider', 'at_generate_slider_code' );

function at_generate_slider_code()
{
    if ( !is_admin() ) {
        wp_enqueue_script( 'owl-scripts' );
        wp_enqueue_script( 'custom-scripts' );
        wp_enqueue_style( 'at-slider-style-public' );
        wp_enqueue_style( 'owl-styles' );
        wp_enqueue_style( 'owl-theme' );
    }
    $images = get_option( 'at-gallery-file' );
    if ( '' !== $images[0] ) {
        $splitImages = explode( ',', $images[0] );
        $html        = '<div class="at-slider owl-carousel owl-theme">';
        foreach ( $splitImages as $image_id ) {
            if ( $image = wp_get_attachment_image_url( $image_id, 'small' ) ) {
                $html .= sprintf( '<div class="at-slider-image"><img src="%s"></div>', esc_url( $image ) );
            }
        }
        $html .= '</div>';
        return $html;
    }
}