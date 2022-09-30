<?php

add_action( 'wp_enqueue_scripts', 'bb_enqueue_assets' );

function bb_enqueue_assets()
{

    wp_enqueue_style( 'bb-styles', get_template_directory_uri() . '/style.css' );

}
