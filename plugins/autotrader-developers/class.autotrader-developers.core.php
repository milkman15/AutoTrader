<?php

class AutoTrader
{

    private static $__show_errors  = false;
    private static $__initiated    = false;
    private static $__helper_admin = null;

    public static $helper = null;

    /**
     * Holds the values to be used in the fields callbacks
     */
    public static $options;

    public static function init()
    {

        if ( !self::$__initiated ) {
            self::load_resources();
            self::__init_hooks();
        }
    }

    private static function __init_hooks()
    {

        self::$__initiated = true;

        add_action( 'wp_enqueue_scripts', ['AutoTrader', 'at_register_scripts'] );
        add_action( 'wp_footer', ['AutoTrader', 'at_generate_developers_feed'] );
        add_shortcode( 'at_slider', ['AutoTrader', 'at_generate_slider_code'] );
    }

    public static function at_register_scripts()
    {
        wp_register_style( 'at-style-public', ATD_URL . '/includes/assets/public/css/style.css' );
        wp_register_style( 'at-slider-style-public', ATD_URL . '/includes/assets/public/css/slider.css' );
        wp_register_script( 'owl-scripts', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js', ['jquery'], '2.3.4', true );
        wp_register_style( 'owl-styles', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css' );
        wp_register_style( 'owl-theme', 'https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css' );
        wp_register_script( 'custom-scripts', ATD_URL . '/includes/assets/public/js/custom.js', ['jquery', 'owl-scripts'], '1.0.0', true );
    }

    public static function at_generate_developers_feed()
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

    public static function at_generate_slider_code()
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

}
