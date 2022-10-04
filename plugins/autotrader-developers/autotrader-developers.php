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

define( 'ATD_FILE', __FILE__ );
define( 'ATD_PATH', plugin_dir_path( ATD_FILE ) );
define( 'ATD_URL', plugin_dir_url( ATD_FILE ) );
define( 'ATD_VERSION', '1.0.0' );

// require ATD_PATH . 'includes/admin/class-atd-admin.php';

add_action( 'wp_enqueue_scripts', 'at_register_scripts' );

if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
    require_once ATD_PATH . 'class.autotrader-developers.admin.core.php';
    add_action( 'init', ['AutoTrader_Admin', 'init'] );
}

function at_register_scripts()
{
    wp_register_style( 'at-style-public', ATD_URL . '/includes/assets/public/css/style.css' );
    wp_register_style( 'at-slider-style-public', ATD_URL . '/includes/assets/public/css/slider.css' );
    wp_register_script( 'owl-scripts', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', ['jquery'], '2.3.4', true );
    wp_register_style( 'owl-styles', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css' );
    wp_register_style( 'owl-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css' );
    wp_register_script( 'custom-scripts', ATD_URL . '/includes/assets/public/js/custom.js', ['jquery', 'owl-scripts'], '1.0.0', true );
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
