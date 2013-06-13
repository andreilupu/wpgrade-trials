<?php
define('ADMIN_AJAX_URL', get_bloginfo('url') . '/wp-admin/admin-ajax.php');
get_template_part('library/inc/util');
get_template_part('library/inc/wpGrade_Trial');
get_template_part('library/inc/config');
get_template_part('library/inc/ajax', 'functions');

add_action('wp_enqueue_scripts', 'load_child_theme_styles', 1);

/*
 * Styles and scripts
 */

function load_child_theme_styles(){

    // styles
    wp_register_style( 'bootstrap', get_template_directory_uri() . '/library/css/bootstrap.min.css' );
    wp_register_style( 'bootstrap-responsive', get_template_directory_uri() . '/library/css/bootstrap-responsive.min.css' );
    wp_register_style( 'main-style', get_template_directory_uri() . '/library/css/style.css', array('bootstrap','bootstrap-responsive') );
    wp_enqueue_style( 'main-style');

    wp_register_script( 'bootstrap-js', get_template_directory_uri() . '/library/js/bootstrap.min.js' );
    wp_register_script( 'scripts-js', get_template_directory_uri() . '/library/js/scripts.js', array( 'jquery', 'bootstrap-js') );
    wp_enqueue_script('scripts-js');

}

/*
 * Some functions
 */

function get_generate_page_id () {
    $pages = get_posts(array(
        'post_type' => 'page',
        'meta_key' => '_wp_page_template',
        'meta_value' => 'template-generate-trial.php'
    ));
    $generate_page_id = false;
    foreach($pages as $page){
        $generate_page_id = $page->ID;
        break;
    }
    return $generate_page_id;
}

/*
 * DEPRICATED
 */

function get_option_index( $option ) {

    global $wpdb;
    $result = $wpdb->get_var("SELECT option_id FROM $wpdb->options WHERE option_name = $option");

    if ( $result ) {
        return $result;
    } else {
        return false;
    }
}

function get_option_name_by_index( $option_id ) {

    global $wpdb;
    $result = $wpdb->get_var("SELECT option_name FROM $wpdb->options WHERE option_id = $option_id");

    if ( $result ) {
        return $result;
    } else {
        return false;
    }
}
