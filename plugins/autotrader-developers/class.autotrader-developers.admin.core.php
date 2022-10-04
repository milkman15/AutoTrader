<?php

class AutoTrader_Admin
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

            self::_init_hooks();
        }
    }

    /**
     * Initializes WordPress hooks
     */
    protected static function _init_hooks()
    {

        self::$__initiated = true;

        add_action( 'admin_enqueue_scripts', ['AutoTrader_Admin', 'at_admin_scripts'], '999' );
        add_action( "admin_init", ['AutoTrader_Admin', 'at_gallery_secttings_page'] );
        add_action( "admin_menu", ['AutoTrader_Admin', 'at_menu_item'] );
        add_action( 'add_meta_boxes', ['AutoTrader_Admin', 'add_developers_meta'] );
        add_action( 'save_post', ['AutoTrader_Admin', 'at_save_developer_meta'] );
    }

    public static function at_admin_scripts( $hook )
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

    public static function at_gallery_secttings_page()
    {
        add_settings_section( "at-gallery-section", "AutoTrader Gallery", null, "at-gallery-files" );
        add_settings_field( "at-gallery-file", "Gallery", ['AutoTrader_Admin', 'at_gallery_display'], "at-gallery-files", "at-gallery-section" );
        register_setting( "at-gallery-section", "at-gallery-file" );
    }

    public static function at_gallery_display()
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

    public static function at_gallery_page()
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
                    <?php

        $images = get_option( 'at-gallery-file' );
        if ( $images && isset( $images ) ) {
            if ( count( $images ) > 0 ): ?>
                <ul class="sortable">
                <?php

            $splitImages = explode( ',', $images[0] );
            foreach ( $splitImages as $image_id ):
                if ( $image = wp_get_attachment_image_url( $image_id, 'medium' ) ): ?>
								                    <li class="ui-state-default">
								                        <div class="at-gallery-image" data-attachId="<?php echo esc_attr( $image_id ); ?>">
								                            <a href="#" class="at-remove">Remove image</a>
								                            <img src="<?php echo esc_url( $image ); ?>" />
								                        </div>
								                    </li>

								                    <?php endif; ?>
            <?php endforeach; ?>
                </ul>
            <?php endif;
        } ?>
                </div>

                <?php submit_button(); ?>
             </form>
            </div>
       <?php
}

    public static function at_menu_item()
    {
        add_submenu_page( "options-general.php", "AutoTrader Gallery", "AutoTrader Gallery", "manage_options", "at-gallery-options", ['AutoTrader_Admin', 'at_gallery_page'] );
    }

    public static function add_developers_meta()
    {
        add_meta_box(
            'at_developers',
            'Developers',
            ['AutoTrader_Admin', 'at_developers_callback'],
            'post',
            'side'
        );
    }

    public static function at_developers_callback( $post )
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

    public static function at_save_developer_meta( $post_id )
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

        if ( !empty( $_POST['at_developers'] ) ) {
            $at_developers = sanitize_post_field( 'at_developers', $_POST['at_developers'], $post_id, 'db' );
            update_post_meta( $post_id, '_at_developers', $at_developers );
        } else {
            delete_post_meta( $post_id, '_at_developers' );
        }
    }

    public static function register_assets( $hook )
    {
        wp_register_style( 'mhmm-admin', MHMM_PLUGIN_URL . 'styles/mhmm-admin.css' );
        wp_register_script( 'mhmm-admin', MHMM_PLUGIN_URL . 'scripts/mhmm-admin.min.js', ['jquery', 'wp-color-picker'], MHMM_VERSION );
        wp_register_style( 'stop-stacking-admin', MHMM_PLUGIN_URL . 'styles/divi-stop-stacking-admin.css' );
    }

    public static function include_assets( $hook )
    {
        wp_enqueue_style( 'mhmm-admin' );
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_style( 'stop-stacking-admin' );
        wp_enqueue_script( 'wp-color-picker' );
        wp_enqueue_script( 'mhmm-admin' );
        wp_localize_script( 'mhmm-admin', 'mhmm_admin_data', ['admin_url' => get_admin_url( null, '/customize.php?et_customizer_option_set=theme' )] );
    }

}
