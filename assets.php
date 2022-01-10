<?php


// in header
add_action('wp_enqueue_scripts', 'add_scripts');
function add_scripts()
{
    $theme_url = get_stylesheet_assets();
    wp_enqueue_style('normalize', $theme_url . '/assets/css/normalize.min.css', array(), 'c3bc3f9dd328c9');

    wp_enqueue_style("rest-styles", $theme_url . "/assets/css/style.min.css", array(), "0bcd9b2d568edd");
    wp_enqueue_style("fonts", $theme_url . "/assets/css/fonts.min.css", array(), "85a002910526ac");

    wp_register_style('Font_Awesome', 'https://pro.fontawesome.com/releases/v5.10.0/css/all.css', array(), null, null);
    wp_enqueue_style('Font_Awesome');
    wp_style_add_data( 'Font_Awesome', array( 'integrity', 'crossorigin' ) , array( 'sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p', 'anonymous' ) );
    // Request page
    // if ( $page_id == 84 ){
    //     wp_enqueue_script( 'paypal-script', 'https://www.paypalobjects.com/api/checkout.js', '8e2d7d10ee9e1f');
    // }

    // We provide data globaly ( for map assets )
    // if ( $page_id == 19 ){
    //     wp_enqueue_style('jq-map-styles', $theme_url . '/assets/css/jqvmap.min.css', array(), 'cdcdaa6ae9b4cf');
    //     wp_enqueue_script( 'jq-map', $theme_url . '/assets/js/jquery.vmap.min.js', array ( 'jquery' ), 'b0fdcae60bc958' );
    //     wp_enqueue_script( 'world-map', $theme_url . '/assets/js/jquery.vmap.world.min.js', array ( 'jquery' ), '1c276e17123611' );
    // }

}

add_action('get_footer', 'add_footer_scripts');
function add_footer_scripts()
{
    $theme_url = get_stylesheet_assets();

    // wp_enqueue_script( 'autogrow', $theme_url . '/assets/js/jquery.ns-autogrow.min.js', array ( 'jquery' ), 'c7233b51fb6611');
    // wp_enqueue_script( 'tooltip', $theme_url . '/assets/js/tooltip.min.js', array ( 'jquery' ), '3c03dafa36b718');
    // wp_enqueue_script( 'users-account-script', $theme_url . '/assets/js/user-account.min.js', array ( 'jquery' ), 'eb1325020c6a5f');




    wp_enqueue_script('main-js', $theme_url . '/assets/js/main-script.min.js', array('jquery'), 'bccb1e07b7deb8');
};
