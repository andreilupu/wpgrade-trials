<?php
/*
Template Name: Generate Trial Page
*/
get_header()  ?>

<div class="span9">
    <div class="hero-unit row">
        <div id="overlay-loader"></div>

        <?php global $trial;
        if ( isset($_GET['token']) && ($_GET['token'] != '') && isset($_GET['token_id']) && ($_GET['token_id'] != '') ) {

            $token_id = base64_decode( $_GET['token_id'] ); ?>

            <h3> Step 1: We are verifing your trial... </h3>

            <?php $token_temp = explode('@@', $token_id);
            $email = $token_temp[0];
            $theme_name = $token_temp[1];
            unset($token_temp);

            $option = get_option('_trial_token_'.$email.'-'.$theme_name);
            $args = $option['args'];
            $username = $args['username'];
            $token = $option['token'];

            if ( $token == $_GET['token'] ) {
                echo '<p class="success" >you are ok!</p>';
            } else {
                echo '<p class="error" >are you sure?!</p>';
            }

            $result = $trial->create_new_trial( $args );

            if ( $result['success'] ) {
                $url = $result['url']; ?>

                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <p>Thank you for your submision! <br /> Check your email <a href="<?php bloginfo('url') ?>">or Go Home</a>. <span class="warning">(hahaha ce imi plac glumele mele)</span></p>
                        <p>
                            Check your new website here: <a href="<?php echo $url; ?>"><?php echo $url; ?></a>
                        </p>
                    </div>

                <?php // time to say bby token
                delete_option('_trial_token_'.$email.'-'.$theme_name);
            }

        } else { ?>

            <div class="alert">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <span class="error">
                    Are you sure you wana be on this page ?
                </span>
            </div>

        <?php }?>

    </div><!--/span-->
</div><!--/row-->
<?php get_footer(); ?>