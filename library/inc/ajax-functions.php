<?php

add_action('wp_ajax_send_confirmation_email', 'send_confirmation_email' );
add_action('wp_ajax_nopriv_send_confirmation_email', 'send_confirmation_email' );

function send_confirmation_email (){
    global $trial;
    ob_start();
    $nonce = $_GET['nonce'];
    $token_id = base64_decode( $_GET['token_id'] );

//    echo '<pre>';
//    var_dump(wp_verify_nonce($nonce, 'confirm_email'));
//    echo '</pre>';

    $token_temp = explode('@@', $token_id);
    $email = $token_temp[0];
    $theme_name = $token_temp[1];
    unset($token_temp);

    $option = get_option('_trial_token_'.$email.'-'.$theme_name);
    $args = $option['args'];
    $username = $args['username'];
    $token = $option['token'];

//    echo '<pre>';
//    var_dump($option);
//    echo '</pre>';

    $generate_page_id = get_generate_page_id();
    $subject = 'Confirm your trial '. $username;
    $path = $theme_name .'-'. $username;

    $message = "Your trial ". $path ." is almost ready.  \r\n\n";
    $message .= "We just need you to confirm it by clicking on <a href=\"". get_page_link($generate_page_id) ."/?token_id=". $_GET['token_id'] ."&token=". $token ."\">this link</a>. \r\n";
    $message .= "Have a nice day!";

    $message .= "You can visit your new trial here <a href=\"http://trial.pixelgrade.com/". $theme_name ."-". $username ."\" :  \r\n";
    $message .= "Or you can log in as administrator here :  \r\n";

    // To send HTML mail, the Content-type header must be set
    $headers = 'MIME-Version: 1.0' . "\r\n";
    $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//    $headers .= 'To: '. $username .' <'. $email .'>' . "\r\n";
    $headers .= 'From: Pixelgrade Media <'. $trial->email_sender .'>' . "\r\n";
    wp_mail($email, $subject, $message, $headers);
    echo ob_get_clean();
    die();


}