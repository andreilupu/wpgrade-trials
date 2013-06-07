<?php

$trial = new wpGrade_Trial();

$username = $_GET['username'];
$email = $_GET['user_email'];
$theme = $_GET['theme'];

if( !isset($_GET['theme']) || empty( $_GET['theme']) )  {

    echo 'You really need to select a theme if you want to test it!';

} else {

    $theme = explode('_', $theme);
    $blog_id = $theme[0];
    $theme_name = $theme[1];

    $user_id = $trial->create_user_for_trial($username, $email);

    if ( is_wp_error( $user_id ) ) {

        echo $user_id->get_error_message();

    } else {

        $trial->create_new_trial($username, $user_id, $email, $blog_id, $theme_name );

    }

    if ( !empty($blog_id) ) {
        echo 'Thank you for your submision! <br /> Check your email or <a href="/">Return</a>.<br />oopps Untill I send the email use "admin" as password';
    }
}
