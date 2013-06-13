<?php
// @TODO Alert user if he already has a trial
global $trial;

$email = $_GET['user_email'];
if ( isset($_GET['username']) && $_GET['username'] != '') {
    $username = $_GET['username'];
} else {
    $username = explode('@',$email);
    $username = $username[0];
}

$theme = $_GET['theme'];

if( !isset($_GET['theme']) || empty( $_GET['theme']) )  {

    echo '<span class="warning">You really need to select a theme if you want to test it!</span>';

} elseif ( !empty($username) ) { // && preg_match( '/^[a-zA-Z0-9]+_?[a-zA-Z0-9]+$/D', $username ) ) { // validate username

    $theme = explode('_', $theme);
    $blog_id = $theme[0];
    $theme_name = $theme[1];

    $user = $trial->create_user($username, $email);

    if ( is_wp_error( $user['id'] ) ) {

        echo $user['id']->get_error_message();

    } else {

        echo '<h2>Step 1: </h2>';

        echo '<div>'.$user['msg'].'</div>';

        $args = array(
            'username' =>  $username,
            'user_id' => $user['id'],
            'email' => $email,
            'blog_id' => $blog_id,
            'theme_name' => $theme_name
        );

        $token = wp_generate_password( $length=12, $include_standard_special_chars=false );
        $option = array(
            'token' => $token,
            'args' => $args
        );

        $path = $theme_name.'-'.$username;
        $option_name = '_trial_token_'.$email.'-'.$theme_name;

        // check if this token option already exists
        $already_token = get_option($option_name);
        $token_id = base64_encode( $email .'@@'. $theme_name );

        echo '<h2>Step 2: </h2>';

        if ( $already_token ) {
            echo '<div class="alert alert-info">';
            echo "<p>You already have an request for this theme.</p>\r\n";
            echo "<p>If you did not got the email with the confirmation link you can <a href=\"".ADMIN_AJAX_URL."?action=send_confirmation_email&nonce=".wp_create_nonce('confirm_email')."&token_id=".$token_id."\">request again</a></p>";
            echo '</div>';
        } else {

            add_option( $option_name , $option);
            $url = ADMIN_AJAX_URL."?action=send_confirmation_email&nonce=".wp_create_nonce('confirm_email')."&token_id=".$token_id;
            $page_id = get_generate_page_id();
            $page_link = get_page_link( $page_id ) ."?token_id=". $token_id ."&token=". $token;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_URL,$url);
            $request_result = curl_exec($ch);
            curl_close($ch);

            if ( $request_result ) {
                echo '<div class="alert alert-success">';
                echo '<p>Confirmation email sent. Until we really send teh email this is the <a href="'.$page_link.'">link</a></p>';
                echo '</div>';
            } else {

                echo '<div class="alert alert-success">';
                echo 'Something weird happened and I could send you the email ... meh here is the link to create your trial anyway: <a href="'.$page_link.'"> the link</a></p>';
                echo '</div>';
            }

        }
    }

    if ( !empty($blog_id) ) {
        // remove the user from main blog
        // @TODO Add him back as trial user
        remove_user_from_blog($user['id'], 1);
    }
} else {

    echo '<div class="alert alert-error">Invallid username</div>';

}
