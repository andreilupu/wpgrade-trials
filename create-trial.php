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
        echo '<pre style="background-color: #ebebeb">';
        $trial->create_new_trial($username, $user_id, $email, $blog_id, $theme_name );
        echo '</pre>';
    }

    if ( !empty($blog_id) ) {
        echo '<p>Thank you for your submision! <br /> Check your email or <a href="/">Return</a>.</p>';

        $user = get_user_by( 'id', $user_id );
        echo 'Hey ' . $username . ' your account is created with the password "admin"';
        echo '<p>Check your new website here: <a href="http://77.81.241.142/'.$theme_name.'-'.$username.'">http://77.81.241.142/'.$theme_name.'-'.$username.'</a></p>';
    }
}
